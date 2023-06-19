<?php

namespace App\Dto\Post;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ModerationPostDto
{
    #[Assert\Type('string')]
    #[SerializedName('moderation_comment')]
    public ?string $moderationComment;

    #[Assert\Range(min: 1, max: 4)]
    public int $status;
}