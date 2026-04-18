<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\ImageRepository;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image implements \Stringable
{
    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'images')]
        #[ORM\JoinColumn(referencedColumnName: 'sku', nullable: false, onDelete: 'CASCADE')]
        private ?Product $product = null,

        #[ORM\Column(type: Types::TEXT)]
        private ?string $originalUrl = null,

        #[ORM\Id]
        #[ORM\Column(type: Types::TEXT)]
        private ?string $code = null,
    ) {
        if ($this->code === null && $this->originalUrl !== null) {
            $this->code = self::calculateCode($this->originalUrl);
        }
    }

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private ?array $resized = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $originalSize = null;

    public static function calculateCode(string $url): string
    {
        return hash('xxh3', $url);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function getResized(): ?array
    {
        return $this->resized;
    }

    public function setResized(?array $resized): self
    {
        $this->resized = $resized;

        return $this;
    }

    public function getOriginalSize(): ?int
    {
        return $this->originalSize;
    }

    public function setOriginalSize(?int $originalSize): self
    {
        $this->originalSize = $originalSize;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->originalUrl;
    }
}
