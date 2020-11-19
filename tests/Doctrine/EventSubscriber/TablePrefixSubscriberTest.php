<?php

namespace Siganushka\GenericBundle\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;

class TablePrefixSubscriberTest extends TestCase
{
    public function testLoadClassMetadata()
    {
        $namingStrategy = new UnderscoreNamingStrategy(CASE_LOWER, true);

        $reflection = new \ReflectionClass(Foo::class);

        $classMetadata = new ClassMetadata(Foo::class, $namingStrategy);
        $classMetadata->table['name'] = $namingStrategy->classToTableName(Foo::class);
        $classMetadata->reflClass = $reflection;
        $classMetadata->namespace = $reflection->getNamespaceName();

        if ($reflection) {
            $classMetadata->name = $reflection->getName();
            $classMetadata->rootEntityName = $reflection->getName();
        }

        $classMetadata->mapManyToMany([
            'fieldName' => 'bars',
            'targetEntity' => 'Bar',
        ]);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);

        $loadClassMetadataEventArgs->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $listener = new TablePrefixSubscriber('app_');
        $listener->loadClassMetadata($loadClassMetadataEventArgs);

        $this->assertSame('app_foo', $classMetadata->getTableName());
        $this->assertSame('app_foo_bar', $classMetadata->associationMappings['bars']['joinTable']['name']);
    }
}

/**
 * @Entity
 */
class Foo
{
}
