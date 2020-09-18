<?php

namespace Siganushka\GenericBundle\Model;

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
