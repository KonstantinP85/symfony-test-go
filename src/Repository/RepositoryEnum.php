<?php

namespace App\Repository;

enum RepositoryEnum: string
{
    case EXPRESSION_AND = 'AND';

    case COMPARISON_EQ = '=';
}
