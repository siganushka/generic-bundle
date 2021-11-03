<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\PreUpdateEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\SortableListener;
use Siganushka\GenericBundle\Entity\SortableInterface;
use Siganushka\GenericBundle\Entity\SortableTrait;

/**
 * @internal
 * @coversNothing
 */
final class SortableListenerTest extends TestCase
{
    private $objectManager;
    private $listener;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->listener = new SortableListener();
    }

    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->listener = null;
    }

    public function testPrePersist(): void
    {
        $foo = new SortableFoo();

        static::assertInstanceOf(SortableInterface::class, $foo);
        static::assertNull($foo->getSorted());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->objectManager);
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
        $preUpdateEventArgs = new PreUpdateEventArgs($foo, $this->objectManager, $changeSet);
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
