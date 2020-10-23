<?php

namespace Siganushka\GenericBundle\Region;

use Siganushka\GenericBundle\Model\RegionInterface;

class ArrayRegionGenerator implements RegionGeneratorInterface
{
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function generate(): ?RegionInterface
    {
        dd($this->array);

        return null;
    }
}
