<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Tests\Fixtures\ApiController;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Siganushka\GenericBundle\Utils\ClassUtils;

class ClassUtilsTest extends TestCase
{
    public function testGenerateAlias(): void
    {
        static::assertSame('class_utils_test', ClassUtils::generateAlias($this));
        static::assertSame('foo', ClassUtils::generateAlias(Foo::class));
        static::assertSame('api_controller', ClassUtils::generateAlias(ApiController::class));
    }
}
