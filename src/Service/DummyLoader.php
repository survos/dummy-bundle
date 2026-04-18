<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Survos\DummyBundle\Entity\Image;
use Survos\DummyBundle\Entity\Product;
use Survos\DummyBundle\Repository\ImageRepository;
use Survos\DummyBundle\Repository\ProductRepository;

final class DummyLoader
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly ImageRepository $imageRepository,
    ) {
    }

    /**
     * @return array{products:int,images:int}
     */
    public function loadProducts(string $source, ?int $limit = null, bool $purge = false): array
    {
        if ($purge) {
            $this->purge();
        }

        $payload = $this->decodePayload($source);
        $products = is_array($payload['products'] ?? null) ? $payload['products'] : [];
        $loadedProducts = 0;
        $loadedImages = 0;

        foreach ($products as $row) {
            if (!is_array($row)) {
                continue;
            }

            $sku = isset($row['sku']) && is_string($row['sku']) ? $row['sku'] : null;
            if ($sku === null || $sku === '') {
                continue;
            }

            $product = $this->productRepository->findOneBySku($sku) ?? new Product($sku, $row);
            $this->entityManager->persist($product);

            $product
                ->setData($row)
                ->setTitle($this->nullableString($row['title'] ?? null))
                ->setDescription($this->nullableString($row['description'] ?? null))
                ->setBrand($this->nullableString($row['brand'] ?? null))
                ->setCategory($this->nullableString($row['category'] ?? null))
                ->setExactPrice(isset($row['price']) ? (float) $row['price'] : null)
                ->setRating((int) round((float) ($row['rating'] ?? 0)))
                ->setStock((int) ($row['stock'] ?? 0))
                ->setTags($this->normalizeStringArray($row['tags'] ?? []));

            foreach ($this->normalizeStringArray($row['images'] ?? []) as $imageUrl) {
                $code = Image::calculateCode($imageUrl);
                $image = $this->imageRepository->findOneByProductAndCode($product, $code) ?? new Image($product, $imageUrl, $code);
                $this->entityManager->persist($image);
                $product->addImage($image);
                ++$loadedImages;
            }

            ++$loadedProducts;
            if ($limit !== null && $loadedProducts >= $limit) {
                break;
            }
        }

        $this->entityManager->flush();

        return [
            'products' => $loadedProducts,
            'images' => $loadedImages,
        ];
    }

    public function purge(): void
    {
        $this->entityManager->createQuery('DELETE FROM Survos\\DummyBundle\\Entity\\Image i')->execute();
        $this->entityManager->createQuery('DELETE FROM Survos\\DummyBundle\\Entity\\Product p')->execute();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(string $source): array
    {
        $json = $this->readSource($source);
        $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    private function readSource(string $source): string
    {
        $contents = @file_get_contents($source);
        if ($contents === false) {
            throw new \RuntimeException(sprintf('Unable to read DummyJSON source "%s".', $source));
        }

        return $contents;
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param mixed $value
     * @return list<string>
     */
    private function normalizeStringArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_string($item) && $item !== ''));
    }
}
