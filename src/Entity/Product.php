<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product', orphanRemoval: true, cascade: ['persist'])]
    private Collection $images;

    /**
     * @var Collection<int, ProductReview>
     */
    #[ORM\OneToMany(targetEntity: ProductReview::class, mappedBy: 'product', orphanRemoval: true, cascade: ['persist'])]
    private Collection $reviews;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $sku,

        #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
        private array $data = [],
    ) {
        $this->images = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    #[ORM\Column(type: Types::INTEGER, unique: true, nullable: true)]
    private ?int $dummyId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $exactPrice = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $rating = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $stock = 0;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private array $tags = [];

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setDummyId(?int $dummyId): self
    {
        $this->dummyId = $dummyId;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function setExactPrice(?float $exactPrice): self
    {
        $this->exactPrice = $exactPrice;

        return $this;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function addReview(ProductReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }
}
