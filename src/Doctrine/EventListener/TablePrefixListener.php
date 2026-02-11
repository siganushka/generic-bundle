<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */
class TablePrefixListener
{
    public function __construct(private readonly string $prefix)
    {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $tablePrefixFn = fn (string $name): string => str_starts_with($name, $this->prefix) ? $name : $this->prefix.$name;

        $metadata = $event->getClassMetadata();
        if (!$metadata->isInheritanceTypeSingleTable() || $metadata->name === $metadata->rootEntityName) {
            $metadata->setPrimaryTable([
                'name' => $tablePrefixFn($metadata->getTableName()),
            ]);
        }

        foreach ($metadata->associationMappings as $mapping) {
            if ($mapping instanceof ManyToManyOwningSideMapping) {
                $mapping->joinTable->name = $tablePrefixFn($mapping->joinTable->name);
            }
        }
    }
}
