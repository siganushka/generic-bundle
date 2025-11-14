<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata as EntityClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\CreatableInterface;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\EnableInterface;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\VersionableInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

use function Symfony\Component\String\u;

class EntityClassMetadataFactory implements ClassMetadataFactoryInterface
{
    public const FIRST_ATTRIBUTES = [
        ResourceInterface::class => 'id',
    ];

    public const LAST_ATTRIBUTES = [
        SortableInterface::class => 'sort',
        VersionableInterface::class => 'version',
        EnableInterface::class => 'enabled',
        CreatableInterface::class => 'createdAt',
        TimestampableInterface::class => 'updatedAt',
        DeletableInterface::class => 'deletedAt',
    ];

    public function __construct(
        private readonly ClassMetadataFactoryInterface $decorated,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public function getMetadataFor(string|object $value): ClassMetadataInterface
    {
        $metadata = $this->decorated->getMetadataFor($value);

        /** @var class-string */
        $entityClass = $metadata->getName();
        /** @var EntityClassMetadata<object>|null */
        $entityMetadata = $this->managerRegistry->getManagerForClass($entityClass)?->getClassMetadata($entityClass);

        if (!$entityMetadata
            || $entityMetadata->isMappedSuperclass
            || $entityMetadata->isEmbeddedClass
            || ($entityMetadata->reflClass?->isAbstract() ?? false)) {
            return $metadata;
        }

        $nameParts = explode('\\', $entityClass);
        $shortName = array_pop($nameParts);
        $resourceName = u($shortName)->snake();

        $firstAttributes = array_flip($this->getSortedAttributes($entityMetadata, self::FIRST_ATTRIBUTES));
        $lastAttributes = array_flip($this->getSortedAttributes($entityMetadata, self::LAST_ATTRIBUTES));

        $attributes = $metadata->getAttributesMetadata();
        $attributesToSort = [];
        $index = 0;

        foreach ($attributes as $attribute => $attributeMetadata) {
            $attributesToSort[$attribute] = match (true) {
                \array_key_exists($attribute, $firstAttributes) => $firstAttributes[$attribute] - 100,
                \array_key_exists($attribute, $lastAttributes) => $lastAttributes[$attribute] + 100,
                default => $index++,
            };

            if ($attributeMetadata->isIgnored() || \count($attributeMetadata->getGroups())) {
                continue;
            }

            /*
             * Group naming strategy:
             *
             * association                  => {entity_name}:{association_name}
             * field/getter/hasser/isser    => item/collection
             */
            if ($entityMetadata->hasAssociation($attribute)) {
                $attributeMetadata->addGroup(\sprintf('%s:%s', $resourceName, u($attribute)->snake()));
            } else {
                $attributeMetadata->addGroup('item');
                $attributeMetadata->addGroup('collection');
            }
        }

        array_multisort($attributesToSort, \SORT_ASC, \SORT_NUMERIC, $attributes);

        $ref = new \ReflectionProperty($metadata, 'attributesMetadata');
        $ref->setAccessible(true);
        $ref->setValue($metadata, $attributes);
        $ref->setAccessible(false);

        return $metadata;
    }

    public function hasMetadataFor(mixed $value): bool
    {
        return $this->decorated->hasMetadataFor($value);
    }

    /**
     * @param EntityClassMetadata<object> $metadata
     * @param array<class-string, string> $interfaces
     *
     * @return array<int, string>
     */
    private function getSortedAttributes(EntityClassMetadata $metadata, array $interfaces): array
    {
        $attributes = [];
        foreach ($interfaces as $interface => $attribute) {
            if ($metadata->getReflectionClass()->implementsInterface($interface)) {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }
}
