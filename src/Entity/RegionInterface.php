<?php

namespace Siganushka\GenericBundle\Entity;

use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;

interface RegionInterface extends TreeNodeInterface
{
    public function getCode(): ?string;

    public function setCode(string $code): self;

    public function getName(): ?string;

    public function setName(string $name): self;
}
