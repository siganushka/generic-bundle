<?php

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Model\TimestampableInterface;

class TimestampableListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        if (null === $entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        if (null === $entity->getUpdatedAt()) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}
