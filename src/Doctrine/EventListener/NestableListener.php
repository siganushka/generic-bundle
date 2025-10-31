<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Siganushka\GenericBundle\Entity\Nestable;

class NestableListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();
        if (!is_subclass_of($metadata->getName(), Nestable::class) || $metadata->hasAssociation('parent')) {
            return;
        }

        $identifier = $metadata->getSingleIdentifierFieldName();
        $metadata->mapManyToOne([
            'targetEntity' => $metadata->getName(),
            'fieldName' => 'parent',
            'inversedBy' => 'children',
            'joinColumns' => [
                [
                    'name' => \sprintf('parent_%s', $identifier),
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
