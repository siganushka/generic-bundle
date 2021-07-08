<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Entity\SortableInterface;
use Siganushka\GenericBundle\Entity\SortableTrait;

/**
 * @internal
 * @coversNothing
 */
final class SortableSubscriberTest extends TestCase
{
    private $entityManager;
    private $listener;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new SortableSubscriber();
    }

    protected function tearDown(): void
    {
        $this->entityManager = null;
        $this->listener = null;
    }

    public function testPrePersist(): void
    {
        $foo = new SortableFoo();

        static::assertInstanceOf(SortableInterface::class, $foo);
        static::assertNull($foo->getSorted());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->entityManager);
        $this->listener->prePersist($lifecycleEventArgs);

        static::assertSame(SortableFoo::DEFAULT_SORTED, $foo->getSorted());

        // set value if not set
        $foo->setSorted(128);
        $this->listener->prePersist($lifecycleEventArgs);

        static::assertSame(128, $foo->getSorted());
    }

    public function testPreUpdate(): void
    {
        $foo = new SortableFoo();

        static::assertInstanceOf(SortableInterface::class, $foo);
        static::assertNull($foo->getSorted());

        $changeSet = [];
        $preUpdateEventArgs = new PreUpdateEventArgs($foo, $this->entityManager, $changeSet);
        $this->listener->preUpdate($preUpdateEventArgs);

        static::assertSame(SortableFoo::DEFAULT_SORTED, $foo->getSorted());

        // set value if not set
        $foo->setSorted(128);
        $this->listener->preUpdate($preUpdateEventArgs);

        static::assertSame(128, $foo->getSorted());
    }
}

class SortableFoo implements SortableInterface
{
    use SortableTrait;
}
