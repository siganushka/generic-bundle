<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Siganushka\GenericBundle\Entity\TimestampableInterface;

class TimestampableListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof TimestampableInterface) {
            return;
        }

        $object->setCreatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof TimestampableInterface) {
            return;
        }

        $object->setUpdatedAt(new \DateTime());
    }
}
