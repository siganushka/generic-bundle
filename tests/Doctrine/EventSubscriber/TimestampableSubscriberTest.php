<?php

namespace Siganushka\GenericBundle\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TimestampableSubscriber;
use Siganushka\GenericBundle\Model\TimestampableInterface;
use Siganushka\GenericBundle\Model\TimestampableTrait;

class TimestampableSubscriberTest extends TestCase
{
    private $entityManager;
    private $listener;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new TimestampableSubscriber();
    }

    protected function tearDown(): void
    {
        $this->entityManager = null;
        $this->listener = null;
    }

    public function testPrePersist()
    {
        $foo = new TimestampableFoo();

        $this->assertInstanceOf(TimestampableInterface::class, $foo);
        $this->assertNull($foo->getCreatedAt());
        $this->assertNull($foo->getUpdatedAt());

        $lifecycleEventArgs = new LifecycleEventArgs($foo, $this->entityManager);
        $this->listener->prePersist($lifecycleEventArgs);

        $this->assertInstanceOf(\DateTimeImmutable::class, $foo->getCreatedAt());
        $this->assertNull($foo->getUpdatedAt());

        // set value if not set
        $foo->setCreatedAt($createdAt = new \DateTimeImmutable());
        $this->listener->prePersist($lifecycleEventArgs);

        $this->assertEquals($createdAt, $foo->getCreatedAt());
    }

    public function testPreUpdate()
    {
        $foo = new TimestampableFoo();

        $this->assertInstanceOf(TimestampableInterface::class, $foo);
        $this->assertNull($foo->getCreatedAt());
        $this->assertNull($foo->getUpdatedAt());

        $changeSet = [];
        $preUpdateEventArgs = new PreUpdateEventArgs($foo, $this->entityManager, $changeSet);
        $this->listener->preUpdate($preUpdateEventArgs);

        $this->assertInstanceOf(\DateTimeInterface::class, $foo->getUpdatedAt());

        // set value if not set
        $foo->setUpdatedAt($updatedAt = new \DateTimeImmutable());
        $this->listener->preUpdate($preUpdateEventArgs);

        $this->assertEquals($updatedAt, $foo->getUpdatedAt());
    }
}

class TimestampableFoo implements TimestampableInterface
{
    use TimestampableTrait;
}
