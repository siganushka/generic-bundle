<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Siganushka\GenericBundle\Entity\AbstractNestable;

class NestableListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();
        if ($metadata->isMappedSuperclass || !is_subclass_of($metadata->getName(), AbstractNestable::class)) {
            return;
        }

        $metadata->mapManyToOne([
            'targetEntity' => $metadata->getName(),
            'fieldName' => 'parent',
            'inversedBy' => 'children',
            'joinColumns' => [
                [
                    'name' => \sprintf('parent_%s', $identifier = $metadata->getSingleIdentifierFieldName()),
                    'referencedColumnName' => $identifier,
                ],
            ],
        ]);

        $metadata->mapOneToMany([
            'targetEntity' => $metadata->getName(),
            'fieldName' => 'children',
            'mappedBy' => 'parent',
            'cascade' => ['all'],
            'orphanRemoval' => true,
        ]);
    }
}
