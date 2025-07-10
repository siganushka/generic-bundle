<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class MappingOverrideListener
{
    public function __construct(private readonly array $mappingOverride = [])
    {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $classMetadata = $event->getClassMetadata();
        if (\array_key_exists($classMetadata->getName(), $this->mappingOverride)) {
            $classMetadata->isMappedSuperclass = true;
        }
    }
}
