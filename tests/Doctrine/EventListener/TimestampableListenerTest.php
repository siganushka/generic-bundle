<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

final class TimestampableListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $foo = new FooTimestampable();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getCreatedAt());

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $listener = new TimestampableListener();
        $listener->prePersist(new PrePersistEventArgs($foo, $entityManager));

        static::assertInstanceOf(\DateTimeImmutable::class, $foo->getCreatedAt());
    }

    public function testPreUpdate(): void
    {
        $foo = new FooTimestampable();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getUpdatedAt());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $changeSet = [];

        $listener = new TimestampableListener();
        $listener->preUpdate(new PreUpdateEventArgs($foo, $entityManager, $changeSet));

        static::assertInstanceOf(\DateTimeInterface::class, $foo->getUpdatedAt());
    }
}

class FooTimestampable implements TimestampableInterface
{
    use TimestampableTrait;
}
