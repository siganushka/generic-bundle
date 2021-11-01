<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
    private $entityManager;
    private $listener;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new TimestampableListener();
    }

    protected function tearDown(): void
    {
        $this->entityManager = null;
        $this->listener = null;
    }

    public function testPrePersist(): void
    {
        $foo = new TimestampableFoo();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getCreatedAt());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->entityManager);
        $this->listener->prePersist($lifecycleEventArgs);

        static::assertInstanceOf(\DateTimeImmutable::class, $foo->getCreatedAt());
    }

    public function testPreUpdate(): void
    {
        $foo = new TimestampableFoo();

        static::assertInstanceOf(TimestampableInterface::class, $foo);
        static::assertNull($foo->getUpdatedAt());

        $changeSet = [];
        $preUpdateEventArgs = new PreUpdateEventArgs($foo, $this->entityManager, $changeSet);
        $this->listener->preUpdate($preUpdateEventArgs);

        static::assertInstanceOf(\DateTimeInterface::class, $foo->getUpdatedAt());
    }
}

class TimestampableFoo implements TimestampableInterface
{
    use TimestampableTrait;
}
