<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata as PersistenceClassMetadata;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand('siganushka:generic:dump-serialization', 'Dump serialization configuration file as YAML.')]
class DumpSerializationCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly ClassMetadataFactoryInterface $metadataFactory,
        private readonly string $serializationDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity', InputArgument::OPTIONAL, 'Entity class name (fqcn) to be dumped.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collectedEntities = [];
        foreach ($this->getAllEntityMetadata($input) as $metadata) {
            $entityName = $metadata->getName();
            if (\array_key_exists($entityName, $collectedEntities)
                || !$metadata instanceof ClassMetadata
                || $metadata->isMappedSuperclass
                || $metadata->isEmbeddedClass
                || ($metadata->reflClass?->isAbstract() ?? false)) {
                continue;
            }

            $serializationMetadata = $this->metadataFactory->getMetadataFor($entityName);
            foreach ($serializationMetadata->getAttributesMetadata() as $attribute => $attributeMetadata) {
                if ($ignore = $attributeMetadata->isIgnored()) {
                    $collectedEntities[$entityName][$attribute]['ignore'] = $ignore;
                    continue;
                }

                $collectedEntities[$entityName][$attribute] = ['groups' => $attributeMetadata->getGroups()] + array_filter([
                    'max_depth' => $attributeMetadata->getMaxDepth(),
                    'serialized_name' => $attributeMetadata->getSerializedName(),
                    'serialized_path' => $attributeMetadata->getSerializedPath() ? (string) $attributeMetadata->getSerializedPath() : null,
                ], fn ($value) => null !== $value);
            }
        }

        // Clears serialization directory after collected data.
        $filesystem = new Filesystem();
        $filesystem->remove($this->serializationDir);

        foreach ($collectedEntities as $entityName => $attributes) {
            $yaml = Yaml::dump([$entityName => compact('attributes')], 4, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

            $fileName = str_replace('\\', '', $entityName);
            $serializationFileName = \sprintf('%s/%s.yaml', $this->serializationDir, $fileName);

            $filesystem->dumpFile($serializationFileName, $yaml);
            $output->writeln(\sprintf('<info>%s -> %s.yaml</info>', $entityName, $fileName));
        }

        return Command::SUCCESS;
    }

    /**
     * @return array<int, PersistenceClassMetadata<object>>
     */
    private function getAllEntityMetadata(InputInterface $input): array
    {
        /** @var class-string|null */
        $entity = $input->getArgument('entity');
        if ($entity && !empty($entity)) {
            $entityMetadata = $this->managerRegistry->getManagerForClass($entity)?->getClassMetadata($entity);
            if (!$entityMetadata instanceof ClassMetadata) {
                throw new \InvalidArgumentException(\sprintf('Class "%s" does not exist', $entity));
            }

            return [$entityMetadata];
        }

        $metadata = [];
        foreach ($this->managerRegistry->getManagers() as $em) {
            array_push($metadata, ...$em->getMetadataFactory()->getAllMetadata());
        }

        return $metadata;
    }
}
