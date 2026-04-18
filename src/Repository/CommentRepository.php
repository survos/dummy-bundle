<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Survos\DummyBundle\Entity\Comment;

/**
 * @extends ServiceEntityRepository<Comment>
 */
final class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }
}
