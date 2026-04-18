<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Survos\DummyBundle\Entity\Image;
use Survos\DummyBundle\Entity\Product;

/**
 * @extends ServiceEntityRepository<Image>
 */
final class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function findOneByProductAndCode(Product $product, string $code): ?Image
    {
        return $this->findOneBy([
            'product' => $product,
            'code' => $code,
        ]);
    }
}
