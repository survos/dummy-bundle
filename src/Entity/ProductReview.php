<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\ProductReviewRepository;

#[ORM\Entity(repositoryClass: ProductReviewRepository::class)]
class ProductReview
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 64)]
        private string $code,
    ) {
    }

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(referencedColumnName: 'sku', nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $rating = 0;

    #[ORM\Column(type: Types::TEXT)]
    private string $comment = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $reviewerName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $reviewerEmail = null;

    public static function calculateCode(string $sku, array $review): string
    {
        return hash('xxh3', $sku.'|'.json_encode($review, JSON_THROW_ON_ERROR));
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): self
    {
        $this->reviewedAt = $reviewedAt;

        return $this;
    }

    public function setReviewerName(?string $reviewerName): self
    {
        $this->reviewerName = $reviewerName;

        return $this;
    }

    public function setReviewerEmail(?string $reviewerEmail): self
    {
        $this->reviewerEmail = $reviewerEmail;

        return $this;
    }
}
