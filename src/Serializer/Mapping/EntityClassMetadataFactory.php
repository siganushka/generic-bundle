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

        if (!$entityMetadata || $entityMetadata->isMappedSuperclass || $entityMetadata->isEmbeddedClass) {
            return $metadata;
        }

        $metadata = $this->decorated->getMetadataFor($value);
        foreach ($metadata->getAttributesMetadata() as $attribute => $attributeMetadata) {
            if ($attributeMetadata->isIgnored()) {
                continue;
            }

            if (\array_key_exists($attribute, $entityMetadata->getAssociationMappings())) {
                $attributeAsSnake = u($attribute)->snake();
                $attributeMetadata->addGroup($attributeAsSnake->prepend('item:')->__toString());
                $attributeMetadata->addGroup($attributeAsSnake->prepend('collection:')->__toString());
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
