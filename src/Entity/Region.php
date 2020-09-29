<?php

namespace App\Entity;

use App\Exception\TreeDescendantConflictException;
use App\Exception\TreeParentConflictException;
use App\Repository\RegionRepository;
use App\Tree\NodeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Model\ResourceInterface;
use Siganushka\GenericBundle\Model\ResourceTrait;

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 */
class Region implements ResourceInterface, RegionInterface
{
    use ResourceTrait;

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

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        if ($parent && $parent->isEqualTo($this) && !$parent->isNew()) {
            throw new TreeParentConflictException($this, $parent);
        }

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

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPinyin(): ?string
    {
        return $this->pinyin;
    }

    public function setPinyin(string $pinyin): self
    {
        $this->pinyin = $pinyin;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
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

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    public function recalculateDepth(): ?int
    {
        if ($this->isRoot()) {
            return $this->depth = 0;
        }

        return $this->depth = $this->getParent()->getDepth() + 1;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAncestors(bool $includeSelf = false): iterable
    {
        $parents = $includeSelf ? [$this] : [];
        $node = $this;

        while ($parent = $node->getParent()) {
            array_unshift($parents, $parent);
            $node = $parent;
        }

        return $parents;
    }

    public function getSiblings(bool $includeSelf = false): iterable
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

    public function getDescendants(bool $includeSelf = false): iterable
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

    public function getRoot(): NodeInterface
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
