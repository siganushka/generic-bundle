<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntityToSuperclassListener
{
    private array $entityToSuperclasses = [];

    public function __construct(array $entityToSuperclasses = [])
    {
        $this->entityToSuperclasses = $entityToSuperclasses;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadata */
        $classMetadata = $event->getClassMetadata();
        if (\in_array($classMetadata->getName(), $this->entityToSuperclasses)) {
            $classMetadata->isMappedSuperclass = true;
            $classMetadata->setCustomRepositoryClass(null);
        }
    }
}
