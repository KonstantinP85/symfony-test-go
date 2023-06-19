<?php

namespace App\Service\GoService\Request;

interface GoServiceRequestInterface
{
    public function getUrl(): Urls;

    public function getMethod(): Method;
}