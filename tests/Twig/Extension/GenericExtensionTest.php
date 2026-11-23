<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Twig\Extension\GenericExtension;
use Siganushka\GenericBundle\Twig\Runtime\GenericExtensionRuntime;

class GenericExtensionTest extends TestCase
{
    public function testGetFilters(): void
    {
        $filters = (new GenericExtension())->getFilters();

        static::assertCount(1, $filters);
        static::assertSame('highlight', $filters[0]->getName());
        static::assertSame([GenericExtensionRuntime::class, 'highlight'], $filters[0]->getCallable());
    }
}
