<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;

final class TablePrefixListenerTest extends TestCase
{
    public function testLoadClassMetadata(): void
    {
        $namingStrategy = new UnderscoreNamingStrategy(\CASE_LOWER);

        $classMetadata = new ClassMetadata(TestCase::class, $namingStrategy);
        $classMetadata->setPrimaryTable([
            'name' => $namingStrategy->classToTableName(TestCase::class),
        ]);

        $classMetadata->mapManyToMany([
            'fieldName' => 'bars',
            'targetEntity' => 'Bar',
        ]);

        $bars = $classMetadata->associationMappings['bars'];
        static::assertInstanceOf(ManyToManyOwningSideMapping::class, $bars);
        static::assertSame('test_case', $classMetadata->getTableName());
        static::assertSame('test_case_bar', $bars->joinTable->name);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $listener = new TablePrefixListener('app_');
        $listener->loadClassMetadata($loadClassMetadataEventArgs);

        $bars = $classMetadata->associationMappings['bars'];
        static::assertInstanceOf(ManyToManyOwningSideMapping::class, $bars);
        static::assertSame('app_test_case', $classMetadata->getTableName());
        static::assertSame('app_test_case_bar', $bars->joinTable->name);
    }
}
