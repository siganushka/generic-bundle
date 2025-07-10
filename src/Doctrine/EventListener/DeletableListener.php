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
            if (!$entity instanceof DeletableInterface) {
                continue;
            }

            $entity->setDeletedAt(new \DateTimeImmutable());
            $em->persist($entity);

            // [important] Manually compute changesets for deleted object
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($entity::class), $entity);
        }
    }
}
