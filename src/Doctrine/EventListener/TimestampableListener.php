<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;

class TimestampableListener
{
    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof CreatableInterface) {
            $object->setCreatedAt(new \DateTimeImmutable());
        }
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof TimestampableInterface) {
            $object->setUpdatedAt(new \DateTime());
        }
    }
}
