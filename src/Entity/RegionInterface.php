<?php

namespace App\Entity;

use App\Tree\NodeInterface;

interface RegionInterface extends NodeInterface
{
    public function getCode(): ?string;
}
