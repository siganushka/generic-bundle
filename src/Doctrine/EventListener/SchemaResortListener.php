<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Siganushka\GenericBundle\Serializer\Mapping\EntityClassMetadataFactory;

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
        /** @param UnqualifiedName $name */
        $unqualifiedNameCallback = fn ($name): string => $name->getIdentifier()->getValue();

        // Compatible Doctrine DBAL 4.2
        $primaryKeyNames = method_exists($table, 'getPrimaryKeyConstraint')
            ? array_map($unqualifiedNameCallback, $table->getPrimaryKeyConstraint()?->getColumnNames() ?? [])
            : $table->getPrimaryKey()?->getColumns() ?? [];

        $foreignKeyNames = [];
        foreach ($table->getForeignKeys() as $item) {
            $foreignColumns = method_exists($item, 'getReferencingColumnNames')
                ? array_map($unqualifiedNameCallback, $item->getReferencingColumnNames())
                : $item->getLocalColumns();

            array_push($foreignKeyNames, ...$foreignColumns);
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
        foreach (EntityClassMetadataFactory::LAST_ATTRIBUTES as $interface => $attribute) {
            if ($metadata->getReflectionClass()->implementsInterface($interface) && \array_key_exists($attribute, $metadata->fieldMappings)) {
                $columnNames[] = $metadata->fieldMappings[$attribute]->columnName;
            }
        }

        return $columnNames;
    }
}
