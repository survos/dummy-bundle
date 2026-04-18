<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Survos\DummyBundle\Entity\Product;

/**
 * @extends ServiceEntityRepository<Product>
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findOneBySku(string $sku): ?Product
    {
        return $this->findOneBy(['sku' => $sku]);
    }
}
