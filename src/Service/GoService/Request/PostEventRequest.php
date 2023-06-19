<?php

namespace App\Service\GoService\Request;

class PostEventRequest implements GoServiceRequestInterface
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

    public function getUrl(): Urls
    {
        return Urls::EVENT_POST;
    }

    public function getMethod(): Method
    {
        return Method::POST;
    }

    public function getPostParam(): array
    {
        return [
            'authorId' => $this->authorId,
            'postId' => $this->postId,
            'postTitle' => $this->postTitle,
            'action' => $this->action
        ];
    }
}