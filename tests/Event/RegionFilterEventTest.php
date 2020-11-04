<?php

namespace Siganushka\GenericBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;

class RegionFilterEventTest extends TestCase
{
    public function testRegionFilterEvent()
    {
        $region1 = new Region();
        $region1->setCode('001');
        $region1->setName('foo1');

        $region2 = new Region();
        $region2->setCode('002');
        $region2->setName('foo2');

        $region3 = new Region();
        $region3->setCode('003');
        $region3->setName('foo3');

        $regions = [
            1 => $region1,
            3 => $region2,
            4 => $region3,
        ];

        $event = new RegionFilterEvent([]);
        $event->setRegions($regions);

        $this->assertSame([0, 1, 2], array_keys($event->getRegions()));
        $this->assertSame(array_values($regions), $event->getRegions());
    }

    public function testRegionFilterEventException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Array of regions must be type of %s', RegionInterface::class));

        new RegionFilterEvent([new \stdClass()]);
    }
}
