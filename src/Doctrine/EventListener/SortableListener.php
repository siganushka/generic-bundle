<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Entity\SortableInterface;

class SortableListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$object instanceof SortableInterface) {
            return;
        }

        $this->setSortedIfNotSet($object);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();
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
