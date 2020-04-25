<?php

namespace Siganushka\GenericBundle\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Model\SortableInterface;
use Siganushka\GenericBundle\Model\SortableTrait;

class SortableSubscriberTest extends TestCase
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

    public function testPrePersist()
    {
        $foo = new SortableFoo();

        $this->assertInstanceOf(SortableInterface::class, $foo);
        $this->assertNull($foo->getSort());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->entityManager);
        $this->listener->prePersist($lifecycleEventArgs);

        $this->assertEquals(SortableFoo::DEFAULT_SORT, $foo->getSort());

        // set value if not set
        $foo->setSort(128);
        $this->listener->prePersist($lifecycleEventArgs);

        $this->assertEquals(128, $foo->getSort());
    }

    public function testPreUpdate()
    {
        $foo = new SortableFoo();

        $this->assertInstanceOf(SortableInterface::class, $foo);
        $this->assertNull($foo->getSort());

        $changeSet = [];
        $preUpdateEventArgs = new PreUpdateEventArgs($foo, $this->entityManager, $changeSet);
        $this->listener->preUpdate($preUpdateEventArgs);

        $this->assertEquals(SortableFoo::DEFAULT_SORT, $foo->getSort());

        // set value if not set
        $foo->setSort(128);
        $this->listener->preUpdate($preUpdateEventArgs);

        $this->assertEquals(128, $foo->getSort());
    }
}

class SortableFoo implements SortableInterface
{
    use SortableTrait;
}
