<?php

namespace Siganushka\GenericBundle\Model;

use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;

interface RegionInterface extends TreeNodeInterface
{
    public function getName(): ?string;

    public function getPinyin(): ?string;

    public function getLatitude(): ?string;

    public function getLongitude(): ?string;
}
