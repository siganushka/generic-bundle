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
        $classMetadata = $event->getClassMetadata();
        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->name === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix.$classMetadata->getTableName(),
            ]);
        }

        foreach ($classMetadata->associationMappings as $mapping) {
            if ($mapping instanceof ManyToManyOwningSideMapping) {
                $mapping->joinTable->name = $this->prefix.$mapping->joinTable->name;
            }
        }
    }
}
