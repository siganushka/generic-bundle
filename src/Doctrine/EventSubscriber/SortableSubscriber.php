<?php

namespace Siganushka\GenericBundle\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Entity\SortableInterface;

class SortableSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SortableInterface) {
            return;
        }

        $this->setSortIfNotSet($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof SortableInterface) {
            return;
        }

        $this->setSortIfNotSet($entity);
    }

    private function setSortIfNotSet(SortableInterface $entity)
    {
        if (null === $entity->getSort()) {
            $entity->setSort(SortableInterface::DEFAULT_SORT);
        }
    }
}
