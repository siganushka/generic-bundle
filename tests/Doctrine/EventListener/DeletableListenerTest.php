<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\DeletableListener;
use Siganushka\GenericBundle\Tests\Fixtures\TestDeletable;

class DeletableListenerTest extends TestCase
{
    public function testIgnoreNonDeletableEntity(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $uow = $this->createMock(UnitOfWork::class);
        $em->method('getUnitOfWork')->willReturn($uow);
        $em->expects(static::never())->method('getClassMetadata');
        $em->expects(static::never())->method('persist');

        $uow->method('getScheduledEntityDeletions')->willReturn([new \stdClass()]);
        $uow->expects(static::never())->method('propertyChanged');
        $uow->expects(static::never())->method('scheduleExtraUpdate');

        (new DeletableListener())->onFlush(new OnFlushEventArgs($em));
    }

    public function testMarkDeletedWithSingleIntegerIdentifier(): void
    {
        $entity = new TestDeletable();

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getIdentifierValues')->with($entity)->willReturn(['id' => 42]);
        $metadata->method('getFieldValue')->with($entity, 'deleted')->willReturn(0);

        $newValue = null;
        $metadata->expects(static::once())
            ->method('setFieldValue')
            ->willReturnCallback(static function (object $object, string $field, mixed $value) use (&$newValue): void {
                $newValue = $value;
            })
        ;

        $em = $this->createMock(EntityManagerInterface::class);
        $uow = $this->createMock(UnitOfWork::class);
        $em->method('getUnitOfWork')->willReturn($uow);
        $em->method('getClassMetadata')->willReturn($metadata);
        $em->expects(static::once())->method('persist')->with($entity);

        $uow->method('getScheduledEntityDeletions')->willReturn([$entity]);
        $uow->expects(static::once())->method('propertyChanged')->with($entity, 'deleted', 0, 42);
        $uow->expects(static::once())->method('scheduleExtraUpdate')->with($entity, ['deleted' => [0, 42]]);

        (new DeletableListener())->onFlush(new OnFlushEventArgs($em));

        static::assertSame(42, $newValue);
    }

    public function testMarkDeletedWithCompositeIdentifier(): void
    {
        $entity = new TestDeletable();

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getIdentifierValues')->with($entity)->willReturn(['foo' => 1, 'bar' => 2]);
        $metadata->method('getFieldValue')->with($entity, 'deleted')->willReturn(0);

        $newValue = null;
        $metadata->method('setFieldValue')
            ->willReturnCallback(static function (object $object, string $field, mixed $value) use (&$newValue): void {
                $newValue = $value;
            })
        ;

        $em = $this->createMock(EntityManagerInterface::class);
        $uow = $this->createMock(UnitOfWork::class);
        $em->method('getUnitOfWork')->willReturn($uow);
        $em->method('getClassMetadata')->willReturn($metadata);

        $uow->method('getScheduledEntityDeletions')->willReturn([$entity]);

        (new DeletableListener())->onFlush(new OnFlushEventArgs($em));

        static::assertIsInt($newValue);
        static::assertGreaterThan(0, $newValue);
        static::assertNotSame(1, $newValue);
        static::assertNotSame(2, $newValue);
    }
}
