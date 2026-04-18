<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\DummyBundle\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'dummy_user')]
class User
{
    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $posts;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::INTEGER)]
        private int $id,
    ) {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private array $data = [];

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
