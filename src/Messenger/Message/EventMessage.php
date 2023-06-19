<?php

namespace App\Messenger\Message;

use App\Messenger\AsyncMessageInterface;

class EventMessage implements AsyncMessageInterface
{
    private int $authorId;

    private int $postId;

    private string $postTitle;

    private string $action;

    public function __construct(
        int $authorId,
        int $postId,
        string $postTitle,
        string $action
    ) {
        $this->authorId = $authorId;
        $this->postId = $postId;
        $this->postTitle = $postTitle;
        $this->action = $action;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}