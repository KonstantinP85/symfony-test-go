<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'app_post')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $content;

    #[ORM\Column(type: 'integer')]
    private int $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $moderationComment;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function __construct(User $user, string $title, string $content, int $status)
    {
        $this->user = $user;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getModerationComment(): string|null
    {
        return $this->moderationComment;
    }

    public function setModerationComment(string|null $moderationComment): void
    {
        $this->moderationComment = $moderationComment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        if ($this->user === $user) {
            return;
        }

        $this->user = $user;
        $user->addPost($this);
    }
}