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
        $addTablePrefix = fn (string $name): string => str_starts_with($name, $this->prefix) ? $name : $this->prefix.$name;

        $classMetadata = $event->getClassMetadata();
        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->name === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $addTablePrefix($classMetadata->getTableName()),
            ]);
        }

        foreach ($classMetadata->associationMappings as $mapping) {
            if ($mapping instanceof ManyToManyOwningSideMapping) {
                $mapping->joinTable->name = $addTablePrefix($mapping->joinTable->name);
            }
        }
    }
}
