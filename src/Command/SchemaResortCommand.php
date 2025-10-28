<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Command;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Exception\ColumnDoesNotExist;
use Doctrine\DBAL\Schema\Exception\TableDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('siganushka:generic:schema-resort', 'Doctrine schema resort.')]
class SchemaResortCommand extends Command
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        $entityManager = $managerRegistry->getManager();
        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \LogicException('The default Doctrine manager is not an ORM EntityManagerInterface.');
        }

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Whether the generated SQL statements will be physically executed in the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->entityManager->getConfiguration();
        $connection = $this->entityManager->getConnection();
        $factory = $this->entityManager->getMetadataFactory();

        $allMetadata = $factory->getAllMetadata();

        $schemaTool = new SchemaTool($this->entityManager);
        $schema = $schemaTool->getSchemaFromMetadata($allMetadata);

        $platform = $connection->getDatabasePlatform();
        $introspectSchema = $connection->createSchemaManager()->introspectSchema();

        $quoteCallback = fn (string $identifier): string => method_exists($connection, 'quoteSingleIdentifier')
                ? $connection->quoteSingleIdentifier($identifier)
                : $connection->quoteIdentifier($identifier);

        $sqls = [];
        foreach ($allMetadata as $metadata) {
            if (\array_key_exists($metadata->name, $sqls)
                || $metadata->isMappedSuperclass
                || $metadata->isEmbeddedClass
                || ($metadata->isInheritanceTypeSingleTable() && $metadata->name !== $metadata->rootEntityName)
                || \in_array($metadata->name, $configuration->getSchemaIgnoreClasses())) {
                continue;
            }

            try {
                $table = $schema->getTable($metadata->getTableName());
                $introspectTable = $introspectSchema->getTable($metadata->getTableName());
            } catch (TableDoesNotExist) {
                continue;
            }

            $columnNames = array_map(fn (Column $item) => $item->getName(), $table->getColumns());
            $introspectColumnNames = array_map(fn (Column $item) => $item->getName(), $introspectTable->getColumns());

            if (\count($columnNames) !== \count($introspectColumnNames)) {
                throw new \RuntimeException('Please run doctrine:schema:update to update the database schema and try again.');
            }

            $columnNamesDiff = array_diff_assoc($columnNames, $introspectColumnNames);
            if ([] === $columnNamesDiff) {
                continue;
            }

            $sqlParts = [];
            foreach ($columnNamesDiff as $index => $columnName) {
                try {
                    $column = $table->getColumn($columnName);
                } catch (ColumnDoesNotExist) {
                    continue;
                }

                $declarationSQL = $platform->getColumnDeclarationSQL($quoteCallback($columnName), $column->toArray());
                if ($lastName = ($columnNames[$index - 1] ?? null)) {
                    $sqlParts[] = \sprintf('MODIFY COLUMN %s AFTER %s', $declarationSQL, $quoteCallback($lastName));
                } else {
                    $sqlParts[] = \sprintf('MODIFY COLUMN %s FIRST', $declarationSQL);
                }
            }

            $sqls[$metadata->name] = \sprintf('ALTER TABLE %s %s', $quoteCallback($table->getName()), implode(', ', $sqlParts));
        }

        $io = new SymfonyStyle($input, $output);
        if (0 === \count($sqls)) {
            $io->success('Nothing to update.');

            return Command::SUCCESS;
        }

        $force = true === $input->getOption('force');
        if ($force) {
            $io->text('Updating database schema...');
            array_walk($sqls, fn (string $sql) => $connection->executeQuery($sql));
            $io->success('Database schema updated successfully!');
        } else {
            $io->info(\sprintf('Using %s --force to updating database schema...', $this->getName()));
            array_walk($sqls, fn (string $sql) => $io->writeln(\sprintf('%s;', $sql)));
        }

        return Command::SUCCESS;
    }
}
