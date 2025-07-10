<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;

class TimestampableListener
{
    public function prePersist(PrePersistEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof CreatableInterface) {
            $object->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof TimestampableInterface) {
            $object->setUpdatedAt(new \DateTime());
        }
    }
}
