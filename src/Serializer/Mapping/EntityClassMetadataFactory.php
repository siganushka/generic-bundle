<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

use function Symfony\Component\String\u;

class EntityClassMetadataFactory implements ClassMetadataFactoryInterface
{
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
        /** @var ClassMetadata<object>|null */
        $entityMetadata = $this->managerRegistry->getManagerForClass($entityClass)?->getClassMetadata($entityClass);

        if (!$entityMetadata
            || $entityMetadata->isMappedSuperclass
            || $entityMetadata->isEmbeddedClass
            || ($entityMetadata->reflClass?->isAbstract() ?? false)) {
            return $metadata;
        }

        $nameParts = explode('\\', $entityClass);
        $shortName = array_pop($nameParts);
        $snakeName = u($shortName)->snake();

        foreach ($metadata->getAttributesMetadata() as $attribute => $attributeMetadata) {
            if ($attributeMetadata->isIgnored()) {
                continue;
            }

            if (\array_key_exists($attribute, $entityMetadata->getAssociationMappings())) {
                $attributeAsSnake = u($attribute)->snake();
                $attributeMetadata->addGroup(\sprintf('item:%s:%s', $snakeName, $attributeAsSnake));
                $attributeMetadata->addGroup(\sprintf('collection:%s:%s', $snakeName, $attributeAsSnake));
            } else {
                $attributeMetadata->addGroup('item');
                $attributeMetadata->addGroup('collection');
            }
        }

        return $metadata;
    }

    public function hasMetadataFor(mixed $value): bool
    {
        return $this->decorated->hasMetadataFor($value);
    }
}
