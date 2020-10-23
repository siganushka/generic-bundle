<?php

namespace Siganushka\GenericBundle\Model;

use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;

interface RegionInterface extends TreeNodeInterface
{
    public function getCode(): ?string;

    public function getName(): ?string;
}
