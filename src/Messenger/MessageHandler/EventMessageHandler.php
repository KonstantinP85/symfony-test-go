<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\EventMessage;
use App\Service\GoService\GoServiceClient;
use App\Service\GoService\Request\PostEventRequest;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EventMessageHandler
{
    public function __construct(private readonly GoServiceClient $goServiceClient)
    {
    }

    public function __invoke(EventMessage $message): void
    {
        $this->goServiceClient->sendPostEvent(
            new PostEventRequest(
                $message->getAuthorId(),
                $message->getPostId(),
                $message->getPostTitle(),
                $message->getAction()
            )
        );
    }
}