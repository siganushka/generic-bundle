<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

use Siganushka\GenericBundle\Utils\ClassUtils;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template T of object = object
 */
abstract class AbstractEntityEvent extends Event
{
    /**
     * @param T $entity
     */
    public function __construct(
        protected readonly object $entity,
        protected readonly array $context = [])
    {
    }

    /**
     * @return T
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function getName(string $entityFqcn): string
    {
        $eventAlias = ClassUtils::generateAlias(static::class);
        $eventAlias = str_replace(['entity_', '_event'], '', $eventAlias);

        return \sprintf('%s.%s', $entityFqcn, $eventAlias);
    }
}
