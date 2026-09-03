<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

/**
 * @template T of object = object
 *
 * @extends AbstractEntityEvent<T>
 */
class EntityBeforeUpdateEvent extends AbstractEntityEvent
{
}
