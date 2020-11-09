<?php

namespace Siganushka\GenericBundle\Tests\Entity;

use Siganushka\GenericBundle\Entity\Region;
use Siganushka\GenericBundle\Entity\RegionInterface;
use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;

class RegionTest extends AbstractRegionTest
{
    public function testRegion()
    {
        $region = new Region();

        $this->assertNull($region->getCode());
        $this->assertNull($region->getName());
        $this->assertInstanceOf(RegionInterface::class, $region);

        $region->setCode('1');
        $region->setName('abcabcabcabcabcabcabcabcabcabcabcabcabc');

        $this->assertSame('100000', $region->getCode());
        $this->assertSame('abcabcabcabcabcabcabcabcabcabcab', $region->getName());
    }

    public function testTreeDescendantConflictException()
    {
        $this->expectException(TreeDescendantConflictException::class);

        $this->province->setParent($this->city);
    }
}
