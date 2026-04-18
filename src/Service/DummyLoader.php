<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Survos\DummyBundle\Entity\Comment;
use Survos\DummyBundle\Entity\Image;
use Survos\DummyBundle\Entity\Post;
use Survos\DummyBundle\Entity\Product;
use Survos\DummyBundle\Entity\ProductReview;
use Survos\DummyBundle\Entity\User;
use Survos\DummyBundle\Repository\CommentRepository;
use Survos\DummyBundle\Repository\ImageRepository;
use Survos\DummyBundle\Repository\PostRepository;
use Survos\DummyBundle\Repository\ProductRepository;
use Survos\DummyBundle\Repository\ProductReviewRepository;
use Survos\DummyBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class DummyLoader
{
    private const USERS_URL = 'https://dummyjson.com/users?limit=1000';
    private const POSTS_URL = 'https://dummyjson.com/posts?limit=1000';
    private const COMMENTS_URL = 'https://dummyjson.com/comments?limit=1000';
    private const PRODUCTS_URL = 'https://dummyjson.com/products?limit=1000';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductReviewRepository $productReviewRepository,
        private readonly ImageRepository $imageRepository,
        #[Autowire(service: 'cache.app')]
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @return array{users:int,posts:int,comments:int,products:int,reviews:int,images:int}
     */
    public function loadAll(bool $purge = false): array
    {
        if ($purge) {
            $this->purge();
        }

        $counts = [
            'users' => 0,
            'posts' => 0,
            'comments' => 0,
            'products' => 0,
            'reviews' => 0,
            'images' => 0,
        ];

        foreach ($this->fetchCollection(self::USERS_URL, 'users') as $row) {
            $id = $this->intOrNull($row['id'] ?? null);
            if ($id === null) {
                continue;
            }

            $user = $this->userRepository->find($id);
            $isNew = $user === null;
            $user ??= new User($id);
            $user
                ->setData($row)
                ->setUsername($this->nullableString($row['username'] ?? null))
                ->setEmail($this->nullableString($row['email'] ?? null))
                ->setFirstName($this->nullableString($row['firstName'] ?? null))
                ->setLastName($this->nullableString($row['lastName'] ?? null))
                ->setImage($this->nullableString($row['image'] ?? null));
            $this->entityManager->persist($user);
            if ($isNew) {
                ++$counts['users'];
            }
        }

        foreach ($this->fetchCollection(self::POSTS_URL, 'posts') as $row) {
            $id = $this->intOrNull($row['id'] ?? null);
            $userId = $this->intOrNull($row['userId'] ?? null);
            if ($id === null || $userId === null) {
                continue;
            }

            $user = $this->userRepository->find($userId);
            if ($user === null) {
                continue;
            }

            $post = $this->postRepository->find($id);
            $isNew = $post === null;
            $post ??= new Post($id);
            $reactions = is_array($row['reactions'] ?? null) ? $row['reactions'] : [];
            $post
                ->setUser($user)
                ->setData($row)
                ->setTitle((string) ($row['title'] ?? ''))
                ->setBody((string) ($row['body'] ?? ''))
                ->setTags($this->normalizeStringArray($row['tags'] ?? []))
                ->setLikes((int) ($reactions['likes'] ?? 0))
                ->setDislikes((int) ($reactions['dislikes'] ?? 0))
                ->setViews((int) ($row['views'] ?? 0));
            $this->entityManager->persist($post);
            if ($isNew) {
                ++$counts['posts'];
            }
        }

        foreach ($this->fetchCollection(self::COMMENTS_URL, 'comments') as $row) {
            $id = $this->intOrNull($row['id'] ?? null);
            $postId = $this->intOrNull($row['postId'] ?? null);
            $userInfo = is_array($row['user'] ?? null) ? $row['user'] : [];
            $userId = $this->intOrNull($userInfo['id'] ?? null);
            if ($id === null || $postId === null || $userId === null) {
                continue;
            }

            $post = $this->postRepository->find($postId);
            $user = $this->userRepository->find($userId);
            if ($post === null || $user === null) {
                continue;
            }

            $comment = $this->commentRepository->find($id);
            $isNew = $comment === null;
            $comment ??= new Comment($id);
            $comment
                ->setPost($post)
                ->setUser($user)
                ->setBody((string) ($row['body'] ?? ''))
                ->setLikes((int) ($row['likes'] ?? 0))
                ->setData($row);
            $this->entityManager->persist($comment);
            if ($isNew) {
                ++$counts['comments'];
            }
        }

        foreach ($this->fetchCollection(self::PRODUCTS_URL, 'products') as $row) {
            $sku = isset($row['sku']) && is_string($row['sku']) ? $row['sku'] : null;
            if ($sku === null || $sku === '') {
                continue;
            }

            $product = $this->productRepository->findOneBySku($sku);
            $isNew = $product === null;
            $product ??= new Product($sku, $row);
            $product
                ->setDummyId($this->intOrNull($row['id'] ?? null))
                ->setData($row)
                ->setTitle($this->nullableString($row['title'] ?? null))
                ->setDescription($this->nullableString($row['description'] ?? null))
                ->setBrand($this->nullableString($row['brand'] ?? null))
                ->setCategory($this->nullableString($row['category'] ?? null))
                ->setExactPrice(isset($row['price']) ? (float) $row['price'] : null)
                ->setRating((int) round((float) ($row['rating'] ?? 0)))
                ->setStock((int) ($row['stock'] ?? 0))
                ->setTags($this->normalizeStringArray($row['tags'] ?? []));
            $this->entityManager->persist($product);
            if ($isNew) {
                ++$counts['products'];
            }

            foreach ($this->normalizeStringArray($row['images'] ?? []) as $imageUrl) {
                $code = Image::calculateCode($imageUrl);
                $image = $this->imageRepository->findOneByProductAndCode($product, $code);
                $wasNew = $image === null;
                $image ??= new Image($product, $imageUrl, $code);
                $this->entityManager->persist($image);
                $product->addImage($image);
                if ($wasNew) {
                    ++$counts['images'];
                }
            }

            $reviews = is_array($row['reviews'] ?? null) ? $row['reviews'] : [];
            foreach ($reviews as $reviewRow) {
                if (!is_array($reviewRow)) {
                    continue;
                }

                $code = ProductReview::calculateCode($sku, $reviewRow);
                $review = $this->productReviewRepository->find($code);
                $wasNew = $review === null;
                $review ??= new ProductReview($code);
                $review
                    ->setProduct($product)
                    ->setRating((int) ($reviewRow['rating'] ?? 0))
                    ->setComment((string) ($reviewRow['comment'] ?? ''))
                    ->setReviewedAt($this->dateOrNull($reviewRow['date'] ?? null))
                    ->setReviewerName($this->nullableString($reviewRow['reviewerName'] ?? null))
                    ->setReviewerEmail($this->nullableString($reviewRow['reviewerEmail'] ?? null));
                $this->entityManager->persist($review);
                $product->addReview($review);
                if ($wasNew) {
                    ++$counts['reviews'];
                }
            }
        }

        $this->entityManager->flush();

        return $counts;
    }

    public function purge(): void
    {
        foreach ([
            'Survos\\DummyBundle\\Entity\\Comment',
            'Survos\\DummyBundle\\Entity\\Post',
            'Survos\\DummyBundle\\Entity\\ProductReview',
            'Survos\\DummyBundle\\Entity\\Image',
            'Survos\\DummyBundle\\Entity\\Product',
            'Survos\\DummyBundle\\Entity\\User',
        ] as $entityClass) {
            $this->entityManager->createQuery(sprintf('DELETE FROM %s e', $entityClass))->execute();
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchCollection(string $url, string $key): array
    {
        $payload = $this->cache->get(md5($url), function (ItemInterface $item) use ($url): array {
            $contents = @file_get_contents($url);
            if ($contents === false) {
                throw new \RuntimeException(sprintf('Unable to read DummyJSON source "%s".', $url));
            }

            $decoded = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

            return is_array($decoded) ? $decoded : [];
        });

        $collection = $payload[$key] ?? [];

        return is_array($collection) ? array_values(array_filter($collection, 'is_array')) : [];
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function dateOrNull(mixed $value): ?\DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
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
