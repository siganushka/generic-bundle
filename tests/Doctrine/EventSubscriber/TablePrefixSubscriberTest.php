<?php

namespace Siganushka\GenericBundle\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;

class TablePrefixSubscriberTest extends TestCase
{
    public function testLoadClassMetadata()
    {
        $namingStrategy = new UnderscoreNamingStrategy(CASE_LOWER, true);

        $classMetadata = new ClassMetadata(Foo::class, $namingStrategy);
        $classMetadata->initializeReflection(new RuntimeReflectionService());

        $classMetadata->mapManyToMany([
            'fieldName' => 'bars',
            'targetEntity' => 'Bar',
        ]);

        $objectManager = $this->createMock(ObjectManager::class);

        $loadClassMetadataEventArgs = new LoadClassMetadataEventArgs($classMetadata, $objectManager);

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
