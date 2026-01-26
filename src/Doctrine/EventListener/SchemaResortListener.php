<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Siganushka\GenericBundle\Serializer\Mapping\EntityMetadataFactory;

class SchemaResortListener
{
    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $event): void
    {
        $table = $event->getClassTable();
        $metadata = $event->getClassMetadata();

        $firstColumnNames = array_flip($this->getFirstColumnNames($table));
        $lastColumnNames = array_flip($this->getLastColumnNames($metadata));

        /** [important] Get columns with key by Reflection */
        $ref = new \ReflectionProperty($table, '_columns');

        /** @var array<string, Column> */
        $columns = $ref->getValue($table);
        $columnsToSrot = [];

        foreach (array_keys($columns) as $index => $columnName) {
            $columnsToSrot[$columnName] = match (true) {
                \array_key_exists($columnName, $firstColumnNames) => $firstColumnNames[$columnName] - 100,
                \array_key_exists($columnName, $lastColumnNames) => $lastColumnNames[$columnName] + 100,
                default => $index,
            };
        }

        array_multisort($columnsToSrot, \SORT_ASC, \SORT_NUMERIC, $columns);
        $ref->setValue($table, $columns);
    }

    /**
     * @return array<int, string>
     */
    private function getFirstColumnNames(Table $table): array
    {
        $unqualifiedName = static fn (UnqualifiedName $name): string => $name->getIdentifier()->getValue();
        $primaryKeyNames = array_map($unqualifiedName, $table->getPrimaryKeyConstraint()?->getColumnNames() ?? []);

        $foreignKeyNames = [];
        foreach ($table->getForeignKeys() as $item) {
            array_push($foreignKeyNames, ...array_map($unqualifiedName, $item->getReferencingColumnNames()));
        }

        return [...$primaryKeyNames, ...$foreignKeyNames];
    }

    /**
     * @param ClassMetadata<object> $metadata
     *
     * @return array<int, string>
     */
    private function getLastColumnNames(ClassMetadata $metadata): array
    {
        $columnNames = [];
        foreach (EntityMetadataFactory::LAST_ATTRIBUTES as $interface => $attribute) {
            if ($metadata->getReflectionClass()->implementsInterface($interface) && \array_key_exists($attribute, $metadata->fieldMappings)) {
                $columnNames[] = $metadata->fieldMappings[$attribute]->columnName;
            }
        }

        return $columnNames;
    }
}
