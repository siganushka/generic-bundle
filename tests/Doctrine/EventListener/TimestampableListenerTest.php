<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\Entity\TimestampableInterface;
use Siganushka\GenericBundle\Entity\TimestampableTrait;

/**
 * @internal
 * @coversNothing
 */
final class TimestampableListenerTest extends TestCase
{
    private $objectManager;
    private $listener;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->listener = new TimestampableListener();
    }

    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->listener = null;
    }

    public function testPrePersist(): void
    {
        $foo = new TimestampableFoo();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getCreatedAt());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->objectManager);
        $this->listener->prePersist($lifecycleEventArgs);

        static::assertInstanceOf(\DateTimeImmutable::class, $foo->getCreatedAt());
    }

    public function testPreUpdate(): void
    {
        $foo = new TimestampableFoo();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getUpdatedAt());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->objectManager);
        $this->listener->preUpdate($lifecycleEventArgs);

        static::assertInstanceOf(\DateTimeInterface::class, $foo->getUpdatedAt());
    }
}

class TimestampableFoo implements TimestampableInterface
{
    use TimestampableTrait;
}
