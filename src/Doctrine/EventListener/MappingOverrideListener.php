<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class MappingOverrideListener
{
    /**
     * @param array<string, string> $mappingOverride
     */
    public function __construct(private readonly array $mappingOverride = [])
    {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadata */
        $classMetadata = $event->getClassMetadata();
        if (\array_key_exists($classMetadata->getName(), $this->mappingOverride)) {
            $classMetadata->isMappedSuperclass = true;
            $classMetadata->setCustomRepositoryClass(null);
        }
    }
}
