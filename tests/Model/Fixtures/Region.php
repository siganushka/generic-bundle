<?php

namespace Siganushka\GenericBundle\Tests\Model\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Siganushka\GenericBundle\Model\RegionInterface;
use Siganushka\GenericBundle\Model\RegionTrait;

class Region implements RegionInterface
{
    use RegionTrait;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }
}
