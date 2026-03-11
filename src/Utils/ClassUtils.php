<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

class ClassUtils
{
    /**
     * @param object|class-string $objectOrClass
     */
    public static function generateAlias(object|string $objectOrClass): string
    {
        $ref = new \ReflectionClass($objectOrClass);
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }
}
