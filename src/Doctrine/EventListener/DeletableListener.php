<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\ResourceInterface;

class DeletableListener
{
    public const FIELD_NAME = 'deleted';

    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof DeletableInterface) {
                $metadata = $em->getClassMetadata($entity::class);

                $oldValue = $metadata->getFieldValue($entity, self::FIELD_NAME);
                // [important] Using unique identifier to ensure multiple delete operations when using a composite index.
                $newValue = ($entity instanceof ResourceInterface ? $entity->getId() : null)
                    ?? (microtime(true) * 1000) % 1000000 * 1000 + random_int(0, 999);

                $metadata->setFieldValue($entity, self::FIELD_NAME, $newValue);
                $em->persist($entity);

                $uow->propertyChanged($entity, self::FIELD_NAME, $oldValue, $newValue);
                $uow->scheduleExtraUpdate($entity, [self::FIELD_NAME => [$oldValue, $newValue]]);
            }
        }
    }
}
