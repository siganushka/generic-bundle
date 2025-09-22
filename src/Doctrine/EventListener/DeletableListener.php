<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Siganushka\Contracts\Doctrine\DeletableInterface;

class DeletableListener
{
    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof DeletableInterface && !$entity->getDeletedAt()) {
                $entity->setDeletedAt(new \DateTimeImmutable());
                $em->persist($entity);

                // [important] Recompute the changeset, forcing delete operations to be update.
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($entity::class), $entity);
            }
        }
    }
}
