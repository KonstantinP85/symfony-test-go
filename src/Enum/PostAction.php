<?php

namespace App\Enum;

enum PostAction: string
{
    case CREATE = 'create';

    case UPDATE = 'update';

    case DELETE = 'delete';
}
