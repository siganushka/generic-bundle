<?php

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Model\SortableInterface;

class SortableListener
{
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
