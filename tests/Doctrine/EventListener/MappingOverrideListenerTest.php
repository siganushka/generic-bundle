<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\MappingOverrideListener;

final class MappingOverrideListenerTest extends TestCase
{
    public function testLoadClassMetadata(): void
    {
        $classMetadata = new ClassMetadata(TestCase::class);
        $classMetadata->setCustomRepositoryClass(EntityRepository::class);

        static::assertFalse($classMetadata->isMappedSuperclass);
        static::assertSame(EntityRepository::class, $classMetadata->customRepositoryClassName);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $listener = new MappingOverrideListener([TestCase::class => 'Bar']);
        $listener->loadClassMetadata($loadClassMetadataEventArgs);

        static::assertTrue($classMetadata->isMappedSuperclass);
        static::assertSame(EntityRepository::class, $classMetadata->customRepositoryClassName);
    }
}
