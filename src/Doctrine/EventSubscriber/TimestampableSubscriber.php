<?php

namespace Siganushka\GenericBundle\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Siganushka\GenericBundle\Entity\TimestampableInterface;

class TimestampableSubscriber implements EventSubscriber
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
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $entity->setCreatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }
}
