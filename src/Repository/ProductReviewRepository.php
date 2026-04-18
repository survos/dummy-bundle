<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Survos\DummyBundle\Entity\ProductReview;

/**
 * @extends ServiceEntityRepository<ProductReview>
 */
final class ProductReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductReview::class);
    }
}
