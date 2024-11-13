<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

final class TimestampableListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $foo = new FooTimestampable();
        static::assertNull($foo->getCreatedAt());

        /** @var ObjectManager */
        $objectManager = $this->createMock(ObjectManager::class);
        $lifecycleEventArgs = new LifecycleEventArgs($foo, $objectManager);

        $listener = new TimestampableListener();
        $listener->prePersist($lifecycleEventArgs);

        static::assertInstanceOf(\DateTimeImmutable::class, $foo->getCreatedAt());
    }

    public function testPreUpdate(): void
    {
        $foo = new FooTimestampable();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getUpdatedAt());

        /** @var ObjectManager */
        $objectManager = $this->createMock(ObjectManager::class);
        $lifecycleEventArgs = new LifecycleEventArgs($foo, $objectManager);

        $listener = new TimestampableListener();
        $listener->preUpdate($lifecycleEventArgs);

        static::assertInstanceOf(\DateTimeInterface::class, $foo->getUpdatedAt());
    }
}

class FooTimestampable implements TimestampableInterface
{
    use TimestampableTrait;
}
