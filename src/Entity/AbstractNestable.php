<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Model\NestableInterface;

/**
 * @implements NestableInterface<static>
 */
#[ORM\MappedSuperclass]
abstract class AbstractNestable implements NestableInterface
{
    /**
     * @var static|null
     */
    protected ?NestableInterface $parent = null;

    /**
     * @var Collection<int, static>
     */
    protected Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return static|null
     */
    public function getParent(): ?NestableInterface
    {
        return $this->parent;
    }

    /**
     * @param static|null $parent
     */
    public function setParent(?NestableInterface $parent): static
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
     * @return Collection<int, static>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(NestableInterface $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(NestableInterface $child): static
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return list<static>
     */
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

    /**
     * @return list<static>
     */
    public function getSiblings(bool $includeSelf = false): array
    {
        $siblings = [];
        foreach ($this->parent?->getChildren() ?? [$this] as $child) {
            if ($includeSelf || $child !== $this) {
                $siblings[] = $child;
            }
        }

        return $siblings;
    }

    /**
     * @return list<static>
     */
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
