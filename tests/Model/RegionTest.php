<?php

namespace Siganushka\GenericBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Entity\Region;
use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;

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
