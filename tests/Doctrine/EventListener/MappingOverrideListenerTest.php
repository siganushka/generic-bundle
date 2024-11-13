<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\MappingOverrideListener;

final class MappingOverrideListenerTest extends TestCase
{
    public function testLoadClassMetadata(): void
    {
        $classMetadata = new ClassMetadata(TestCase::class);
        $classMetadata->setCustomRepositoryClass(EntityRepository::class);

        static::assertFalse($classMetadata->isMappedSuperclass);
        static::assertNotNull($classMetadata->customRepositoryClassName);

        /** @var MockObject&LoadClassMetadataEventArgs */
        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $listener = new MappingOverrideListener([TestCase::class => 'Bar']);
        $listener->loadClassMetadata($loadClassMetadataEventArgs);

        static::assertTrue($classMetadata->isMappedSuperclass);
        static::assertNull($classMetadata->customRepositoryClassName);
    }
}
