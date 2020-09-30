<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;
use Siganushka\GenericBundle\Exception\TreeDescendantConflictException;

trait RegionTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="children", cascade={"all"})
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=12, unique=true, options={"fixed": true})
     */
    private $code;

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
     * @ORM\Column(type="smallint")
     */
    private $depth;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="parent", cascade={"all"})
     */
    private $children;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): RegionInterface
    {
        $this->code = $code;

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

    public function getDepth(): int
    {
        if (null === $this->depth) {
            $this->recalculateDepth();
        }

        return $this->depth;
    }

    public function setDepth(int $depth): TreeNodeInterface
    {
        $this->depth = $depth;

        return $this;
    }

    public function recalculateDepth(): TreeNodeInterface
    {
        if ($this->isRoot()) {
            return $this->depth = 0;
        }

        $this->depth = $this->getParent()->getDepth() + 1;

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
}
