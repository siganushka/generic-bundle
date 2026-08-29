<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Event;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Event\AbstractEntityEvent;
use Siganushka\GenericBundle\Event\EntityBeforeCreateEvent;
use Siganushka\GenericBundle\Event\EntityBeforeDeleteEvent;
use Siganushka\GenericBundle\Event\EntityBeforeUpdateEvent;
use Siganushka\GenericBundle\Event\EntityCreatedEvent;
use Siganushka\GenericBundle\Event\EntityDeletedEvent;
use Siganushka\GenericBundle\Event\EntityUpdatedEvent;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;

class EntityEventTest extends TestCase
{
    /**
     * @param class-string $eventClass
     */
    #[DataProvider('entityEventProvider')]
    public function testAll(string $eventClass, string $eventAlias): void
    {
        /** @var AbstractEntityEvent */
        $event = (new \ReflectionClass($eventClass))->newInstance($entity = new Foo());

        static::assertSame($entity, $event->getEntity());
        static::assertSame([], $event->getContext());
        static::assertSame($entity::class.'.'.$eventAlias, $event->getEventName());
    }

    public static function entityEventProvider(): iterable
    {
        yield [EntityBeforeCreateEvent::class, 'before_create'];
        yield [EntityBeforeUpdateEvent::class, 'before_update'];
        yield [EntityBeforeDeleteEvent::class, 'before_delete'];
        yield [EntityCreatedEvent::class, 'created'];
        yield [EntityUpdatedEvent::class, 'updated'];
        yield [EntityDeletedEvent::class, 'deleted'];
    }
}
