<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Enum\PostAction;
use App\Messenger\Message\EventMessage;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class PostChangeListener
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $om = $eventArgs->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Post) {

                $this->bus->dispatch(new EventMessage(
                    $entity->getUser()->getId(),
                    $entity->getId(),
                    $entity->getTitle(),
                    PostAction::CREATE->value
                ));
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Post) {

                $this->bus->dispatch(new EventMessage(
                    $entity->getUser()->getId(),
                    $entity->getId(),
                    $entity->getTitle(),
                    PostAction::UPDATE->value
                ));
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof Post) {

                $this->bus->dispatch(new EventMessage(
                    $entity->getUser()->getId(),
                    $entity->getId(),
                    $entity->getTitle(),
                    PostAction::DELETE->value
                ));
            }
        }
    }
}