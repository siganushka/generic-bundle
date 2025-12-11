<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Mapping\EntityMetadataFactory;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

class EntityMetadataFactoryTest extends TestCase
{
    public function testAll(): void
    {
        $foo = $this->createMock(ClassMetadata::class);
        $foo->isMappedSuperclass = true;
        $foo->isEmbeddedClass = true;

        $bar = $this->createMock(ClassMetadata::class);
        $bar->expects(static::any())
            ->method('hasField')
            ->willReturnCallback(fn (string $fieldName) => \in_array($fieldName, ['x', 'y']))
        ;

        $bar->expects(static::any())
            ->method('hasAssociation')
            ->willReturnCallback(fn (string $fieldName) => 'testSnakeName' === $fieldName)
        ;

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects(static::any())
            ->method('getClassMetadata')
            ->willReturnCallback(fn (string $className) => Foo::class === $className ? $foo : $bar)
        ;

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects(static::any())
            ->method('getManagerForClass')
            ->willReturnCallback(fn (string $class) => \in_array($class, [Foo::class, Bar::class]) ? $objectManager : null);

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        static::assertSame(['x', 'y'], array_keys($classMetadataFactory->getMetadataFor(Foo::class)->getAttributesMetadata()));

        $entityMetadataFactory = new EntityMetadataFactory($classMetadataFactory, $managerRegistry);
        static::assertTrue($entityMetadataFactory->hasMetadataFor(Foo::class));
        static::assertTrue($entityMetadataFactory->hasMetadataFor(Bar::class));
    }
}
