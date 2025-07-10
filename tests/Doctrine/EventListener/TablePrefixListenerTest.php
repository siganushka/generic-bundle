<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\JoinTableMapping;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;

final class TablePrefixListenerTest extends TestCase
{
    public function testLoadClassMetadata(): void
    {
        $namingStrategy = new UnderscoreNamingStrategy(\CASE_LOWER);

        $name = $namingStrategy->classToTableName(TestCase::class);
        $classMetadata = new ClassMetadata(TestCase::class, $namingStrategy);
        $classMetadata->setPrimaryTable(['name' => $name]);

        $classMetadata->mapManyToMany([
            'fieldName' => 'bars',
            'targetEntity' => 'Bar',
        ]);

        /** @var JoinTableMapping */
        $joinTable = $classMetadata->associationMappings['bars']['joinTable'];
        static::assertSame('test_case', $classMetadata->getTableName());
        static::assertSame('test_case_bar', $joinTable->name);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $listener = new TablePrefixListener('app_');
        $listener->loadClassMetadata($loadClassMetadataEventArgs);

        /** @var JoinTableMapping */
        $joinTable = $classMetadata->associationMappings['bars']['joinTable'];
        static::assertSame('app_test_case', $classMetadata->getTableName());
        static::assertSame('app_test_case_bar', $joinTable->name);
    }
}
