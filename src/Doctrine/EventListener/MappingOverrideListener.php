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
            // [important] Duplicate definition of column "xxx"
            foreach (array_keys($classMetadata->embeddedClasses) as $embeddedField) {
                foreach (array_keys($classMetadata->fieldMappings) as $field) {
                    if (str_starts_with($field, $embeddedField.'.')) {
                        unset($classMetadata->fieldMappings[$field]);
                    }
                }
            }
        }
    }
}
