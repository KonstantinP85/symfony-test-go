<?php

namespace App\Enum;

enum PostStatus: int
{
    case UNKNOWN = 1;

    case PUBLISHED = 2;

    case ON_MODERATION = 3;

    case MODERATION_REFUSED = 4;
}
