<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @template TNode of NestableInterface
 */
interface NestableInterface
{
    /**
     * @return TNode|null
     */
    public function getParent(): ?self;

    /**
     * @param TNode|null $parent
     */
    public function setParent(?self $parent): static;

    /**
     * @return Collection<int, TNode>
     */
    public function getChildren(): Collection;

    /**
     * @param TNode $child
     */
    public function addChild(self $child): static;

    /**
     * @param TNode $child
     */
    public function removeChild(self $child): static;

    /**
     * @return list<TNode>
     */
    public function getAncestors(bool $includeSelf = false): array;

    /**
     * @return list<TNode>
     */
    public function getSiblings(bool $includeSelf = false): array;

    /**
     * @return list<TNode>
     */
    public function getDescendants(bool $includeSelf = false): array;

    public function getDepth(): int;

    public function isRoot(): bool;

    public function isLeaf(): bool;
}
