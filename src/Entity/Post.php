<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\PostRepository;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::INTEGER)]
        private int $id,
    ) {
        $this->comments = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private array $data = [];

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $body = '';

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private array $tags = [];

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $likes = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $dislikes = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $views = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }
}
