<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\EventListener;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\EnableInterface;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\VersionableInterface;

class SchemaResortListener
{
    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $event): void
    {
        $table = $event->getClassTable();
        $metadata = $event->getClassMetadata();

        $firstColumnNames = array_flip(self::getFirstColumnNames($table));
        $lastColumnNames = array_flip(self::getLastColumnNames($metadata));

        /** [important] Get columns with key by Reflection */
        $ref = new \ReflectionProperty($table, '_columns');
        $ref->setAccessible(true);

        /** @var array<string, Column> */
        $columns = $ref->getValue($table);
        /** @var array<string, int> */
        $columnNamesToSort = [];

        foreach (array_keys($columns) as $index => $columnName) {
            $columnNamesToSort[$columnName] = match (true) {
                \array_key_exists($columnName, $firstColumnNames) => $firstColumnNames[$columnName] - 100,
                \array_key_exists($columnName, $lastColumnNames) => $lastColumnNames[$columnName] + 100,
                default => $index,
            };
        }

        array_multisort($columnNamesToSort, \SORT_ASC, \SORT_NUMERIC, $columns);

        $ref->setValue($table, $columns);
        $ref->setAccessible(false);
    }

    /**
     * @return array<int, string>
     */
    public static function getFirstColumnNames(Table $table): array
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
     * @param ClassMetadata<object> $classMetadata
     *
     * @return array<int, string>
     */
    public static function getLastColumnNames(ClassMetadata $classMetadata): array
    {
        $interfaces = [
            SortableInterface::class => 'sort',
            VersionableInterface::class => 'version',
            EnableInterface::class => 'enabled',
            CreatableInterface::class => 'createdAt',
            TimestampableInterface::class => 'updatedAt',
            DeletableInterface::class => 'deletedAt',
        ];

        $lastColumnNames = [];
        foreach ($interfaces as $interface => $fieldName) {
            if ($classMetadata->getReflectionClass()->implementsInterface($interface) && \array_key_exists($fieldName, $classMetadata->fieldMappings)) {
                $lastColumnNames[] = $classMetadata->fieldMappings[$fieldName]->columnName;
            }
        }

        return $lastColumnNames;
    }
}
