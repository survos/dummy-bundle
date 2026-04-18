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

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $sku,

        #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
        private array $data = [],
    ) {
        $this->images = new ArrayCollection();
    }

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

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getExactPrice(): ?float
    {
        return $this->exactPrice;
    }

    public function setExactPrice(?float $exactPrice): self
    {
        $this->exactPrice = $exactPrice;

        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        $thumbnail = $this->data['thumbnail'] ?? null;

        return is_string($thumbnail) ? $thumbnail : null;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }
}
