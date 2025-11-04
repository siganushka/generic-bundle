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
use Symfony\Component\Serializer\Mapping\ClassMetadata;
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

            if ($attributeMetadata->isIgnored()) {
                continue;
            }

            if (\array_key_exists($attribute, $entityMetadata->getAssociationMappings())) {
                $attributeAsSnake = u($attribute)->snake();
                $attributeMetadata->addGroup(\sprintf('item:%s', $attributeAsSnake));
                $attributeMetadata->addGroup(\sprintf('collection:%s', $attributeAsSnake));
            } else {
                $attributeMetadata->addGroup('item');
                $attributeMetadata->addGroup('collection');
            }
        }

        array_multisort($attributesToSort, \SORT_ASC, \SORT_NUMERIC, $attributes);

        if ($metadata instanceof ClassMetadata) {
            $metadata->attributesMetadata = $attributes;
        }

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
