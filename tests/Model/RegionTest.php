<?php

namespace Siganushka\GenericBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;
use Siganushka\GenericBundle\Model\Region;

class RegionTest extends TestCase
{
    public function testTreeDescendantConflictException()
    {
        $this->expectException(TreeDescendantConflictException::class);

        $foo = new Region();

        $bar = new Region();
        $bar->addChild($foo);
        $bar->setParent($foo);
    }
}
