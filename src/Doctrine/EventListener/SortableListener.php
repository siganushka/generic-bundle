<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Entity\SortableInterface;

class SortableListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SortableInterface) {
            return;
        }

        $this->setSortedIfNotSet($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SortableInterface) {
            return;
        }

        $this->setSortedIfNotSet($entity);
    }

    private function setSortedIfNotSet(SortableInterface $entity): void
    {
        if (null === $entity->getSorted()) {
            $entity->setSorted(SortableInterface::DEFAULT_SORTED);
        }
    }
}
