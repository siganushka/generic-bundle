<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Siganushka\GenericBundle\Entity\TimestampableInterface;

class TimestampableListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

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
