<?php

namespace Siganushka\GenericBundle\Tree;

use Doctrine\Common\Collections\Collection;

interface TreeNodeInterface
{
    public function getParent(): ?self;

    public function setParent(?self $parent): self;

    public function getChildren(): Collection;

    public function getAncestors(bool $includeSelf = false): array;

    public function getSiblings(bool $includeSelf = false): array;

    public function getDescendants(bool $includeSelf = false): array;

    public function getDepth(): int;

    public function getRoot(): self;

    public function isRoot(): bool;

    public function isLeaf(): bool;
}
