<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Siganushka\GenericBundle\Entity\SortableInterface;

class SortableListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof SortableInterface) {
            return;
        }

        $this->setSortedIfNotSet($object);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof SortableInterface) {
            return;
        }

        $this->setSortedIfNotSet($object);
    }

    private function setSortedIfNotSet(SortableInterface $object): void
    {
        if (null === $object->getSorted()) {
            $object->setSorted(SortableInterface::DEFAULT_SORTED);
        }
    }
}
