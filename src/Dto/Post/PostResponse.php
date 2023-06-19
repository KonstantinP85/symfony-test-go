<?php

namespace App\Dto\Post;

use Symfony\Component\Serializer\Annotation\Groups;

class PostResponse
{
    #[Groups(['guest', 'author', 'moderator'])]
    public string $title;

    #[Groups(['guest', 'author', 'moderator'])]
    public string $content;

    #[Groups(['author', 'moderator'])]
    public int $status;

    #[Groups(['author', 'moderator'])]
    public int $user;

    #[Groups(['author', 'moderator'])]
    public ?string $moderationComment;
}