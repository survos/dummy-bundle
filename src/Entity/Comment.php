<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\CommentRepository;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::INTEGER)]
        private int $id,
    ) {
    }

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $body = '';

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $likes = 0;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private array $data = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
