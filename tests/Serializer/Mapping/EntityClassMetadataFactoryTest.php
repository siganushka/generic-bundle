<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Mapping\EntityClassMetadataFactory;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

class EntityClassMetadataFactoryTest extends TestCase
{
    public function testAll(): void
    {
        $foo = $this->createMock(ClassMetadata::class);
        $foo->isMappedSuperclass = true;
        $foo->isEmbeddedClass = true;

        $bar = $this->createMock(ClassMetadata::class);
        $bar->expects(static::any())
            ->method('getAssociationMappings')
            ->willReturn(['testSnakeName' => true])
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

        $factory = new EntityClassMetadataFactory(new ClassMetadataFactory(new AttributeLoader()), $managerRegistry);
        static::assertTrue($factory->hasMetadataFor(Foo::class));
        static::assertTrue($factory->hasMetadataFor(Bar::class));

        $fooMetadata = $factory->getMetadataFor(Foo::class)->getAttributesMetadata();
        static::assertSame([], $fooMetadata['x']->getGroups());
        static::assertSame([], $fooMetadata['y']->getGroups());

        $barMetadata = $factory->getMetadataFor(Bar::class)->getAttributesMetadata();
        static::assertSame(['item', 'collection'], $barMetadata['x']->getGroups());
        static::assertSame(['item', 'collection'], $barMetadata['y']->getGroups());
        static::assertSame(['item:bar:test_snake_name', 'collection:bar:test_snake_name'], $barMetadata['testSnakeName']->getGroups());
        static::assertSame([], $barMetadata['testIgnore']->getGroups());
        static::assertTrue($barMetadata['testIgnore']->isIgnored());
    }
}
