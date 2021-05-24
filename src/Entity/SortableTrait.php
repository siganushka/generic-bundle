<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait SortableTrait
{
    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"sortable"})
     */
    private $sort;

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort)
    {
        $this->sort = $sort;

        return $this;
    }
}
