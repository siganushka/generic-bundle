<?php

namespace Siganushka\GenericBundle\Region;

use Siganushka\GenericBundle\Model\RegionInterface;

interface RegionGeneratorInterface
{
    public function generate(): ?RegionInterface;
}
