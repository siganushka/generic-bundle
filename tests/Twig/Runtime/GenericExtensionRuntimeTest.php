<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Twig\Runtime;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Twig\Runtime\GenericExtensionRuntime;

class GenericExtensionRuntimeTest extends TestCase
{
    public function testAll(): void
    {
        $runtime = new GenericExtensionRuntime();
        static::assertSame('foo', $runtime->highlight('foo', ''));
        static::assertSame('', $runtime->highlight('', 'foo'));
        static::assertSame('<strong class="text-danger">foo</strong>', $runtime->highlight('foo', 'foo'));
        static::assertSame('Hello <strong class="text-danger">World</strong>', $runtime->highlight('Hello World', 'world'));
        static::assertSame('Foo<strong class="text-danger">Ba</strong>r<strong class="text-danger">Ba</strong>z', $runtime->highlight('FooBarBaz', 'ba'));
        static::assertSame('你<strong class="foo">好呀</strong>！', $runtime->highlight('你好呀！', '好呀', 'foo'));
    }
}
