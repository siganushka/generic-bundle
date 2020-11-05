<?php

namespace Siganushka\GenericBundle\Manager;

use Siganushka\GenericBundle\Model\RegionInterface;

interface RegionManagerInterface
{
    public function getProvinces(): array;

    public function getChildrenByParent(?RegionInterface $parent): array;
}
