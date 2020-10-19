<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;
use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;

/**
 * @ORM\Entity
 */
class Region implements ResourceInterface, RegionInterface
{
    use ResourceTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="children", cascade={"all"})
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=64)
     */
    private $pinyin;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=6)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="parent", cascade={"all"})
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?TreeNodeInterface
    {
        return $this->parent;
    }

    public function setParent(?TreeNodeInterface $parent): TreeNodeInterface
    {
        if ($parent && \in_array($parent, $this->getDescendants(), true)) {
            throw new TreeDescendantConflictException($this, $parent);
        }

        $this->parent = $parent;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): RegionInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getPinyin(): ?string
    {
        return $this->pinyin;
    }

    public function setPinyin(string $pinyin): RegionInterface
    {
        $this->pinyin = $pinyin;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): RegionInterface
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): RegionInterface
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(TreeNodeInterface $child): TreeNodeInterface
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(TreeNodeInterface $child): TreeNodeInterface
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAncestors(bool $includeSelf = false): array
    {
        $parents = $includeSelf ? [$this] : [];
        $node = $this;

        while ($parent = $node->getParent()) {
            array_unshift($parents, $parent);
            $node = $parent;
        }

        return $parents;
    }

    public function getSiblings(bool $includeSelf = false): array
    {
        if ($this->isRoot()) {
            return [];
        }

        $siblings = [];
        foreach ($this->getParent()->getChildren() as $child) {
            if ($includeSelf || !$this->isEqualTo($child)) {
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

    public function getRoot(): TreeNodeInterface
    {
        $node = $this;

        while ($parent = $node->getParent()) {
            $node = $parent;
        }

        return $node;
    }

    public function isRoot(): bool
    {
        return null === $this->getParent();
    }

    public function isLeaf(): bool
    {
        return 0 === \count($this->children);
    }

    public function getDepth(): int
    {
        if ($this->isRoot()) {
            return 0;
        }

        return $this->getParent()->getDepth() + 1;
    }
}
