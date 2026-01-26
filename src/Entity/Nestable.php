<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Repository\NestableRepository;

/**
 * @template TNode of Nestable = Nestable
 */
#[ORM\MappedSuperclass(repositoryClass: NestableRepository::class)]
class Nestable
{
    /**
     * @var TNode|null
     */
    protected ?self $parent = null;

    /**
     * @var Collection<int, TNode>
     */
    protected Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return TNode|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param TNode|null $parent
     */
    public function setParent(?self $parent): static
    {
        if ($parent && $parent === $this) {
            throw new \InvalidArgumentException('The parent conflict has been detected.');
        }

        if ($parent && \in_array($parent, $this->getDescendants(), true)) {
            throw new \InvalidArgumentException('The descendants conflict has been detected.');
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, TNode>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param TNode $child
     */
    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param TNode $child
     */
    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAncestors(bool $includeSelf = false): array
    {
        $ancestors = $includeSelf ? [$this] : [];
        $node = $this;

        while ($parent = $node->getParent()) {
            array_unshift($ancestors, $parent);
            $node = $parent;
        }

        return $ancestors;
    }

    public function getSiblings(bool $includeSelf = false): array
    {
        $siblings = [];
        foreach ($this->parent?->getChildren() ?? [] as $child) {
            if ($includeSelf || $child !== $this) {
                $siblings[] = $child;
            }
        }

        return $siblings;
    }

    public function getDescendants(bool $includeSelf = false): array
    {
        $descendants = $includeSelf ? [$this] : [];
        foreach ($this->children as $child) {
            $descendants[] = $child;
            if (!$child->isLeaf()) {
                $descendants = array_merge($descendants, $child->getDescendants());
            }
        }

        return $descendants;
    }

    public function getDepth(): int
    {
        return $this->parent ? $this->parent->getDepth() + 1 : 0;
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function isLeaf(): bool
    {
        return $this->children->isEmpty();
    }
}
