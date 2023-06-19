<?php

namespace App\Dto\Post;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePostDto
{
    #[Assert\Type('string')]
    public string $title;

    #[Assert\Type('string')]
    public string $content;
}