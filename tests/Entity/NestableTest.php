<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Tests\Fixtures\TestNested;

class NestableTest extends TestCase
{
    public function testAll(): void
    {
        $nested = new TestNested();

        $nested->addChild($nested_1 = new TestNested());
        $nested->addChild($nested_2 = new TestNested());
        $nested->addChild($nested_3 = new TestNested());

        $nested_3->addChild($nested_3_1 = new TestNested());

        static::assertNull($nested->getParent());
        static::assertSame([$nested_1, $nested_2, $nested_3], $nested->getChildren()->toArray());
        static::assertSame([], $nested->getAncestors());
        static::assertSame([$nested], $nested->getAncestors(true));
        static::assertSame([], $nested->getSiblings());
        static::assertSame([$nested], $nested->getSiblings(true));
        static::assertSame([$nested_1, $nested_2, $nested_3, $nested_3_1], $nested->getDescendants());
        static::assertSame([$nested, $nested_1, $nested_2, $nested_3, $nested_3_1], $nested->getDescendants(true));
        static::assertSame(0, $nested->getDepth());
        static::assertTrue($nested->isRoot());
        static::assertFalse($nested->isLeaf());

        static::assertSame($nested, $nested_3->getParent());
        static::assertSame([$nested_3_1], $nested_3->getChildren()->toArray());
        static::assertSame([$nested], $nested_3->getAncestors());
        static::assertSame([$nested, $nested_3], $nested_3->getAncestors(true));
        static::assertSame([$nested_1, $nested_2], $nested_3->getSiblings());
        static::assertSame([$nested_1, $nested_2, $nested_3], $nested_3->getSiblings(true));
        static::assertSame([$nested_3_1], $nested_3->getDescendants());
        static::assertSame([$nested_3, $nested_3_1], $nested_3->getDescendants(true));
        static::assertSame(1, $nested_3->getDepth());
        static::assertFalse($nested_3->isRoot());
        static::assertFalse($nested_3->isLeaf());

        static::assertSame($nested_3, $nested_3_1->getParent());
        static::assertSame([], $nested_3_1->getChildren()->toArray());
        static::assertSame([$nested, $nested_3], $nested_3_1->getAncestors());
        static::assertSame([$nested, $nested_3, $nested_3_1], $nested_3_1->getAncestors(true));
        static::assertSame([], $nested_3_1->getSiblings());
        static::assertSame([$nested_3_1], $nested_3_1->getSiblings(true));
        static::assertSame([], $nested_3_1->getDescendants());
        static::assertSame([$nested_3_1], $nested_3_1->getDescendants(true));
        static::assertSame(2, $nested_3_1->getDepth());
        static::assertFalse($nested_3_1->isRoot());
        static::assertTrue($nested_3_1->isLeaf());
    }
}
