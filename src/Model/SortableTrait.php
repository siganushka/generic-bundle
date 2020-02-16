<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SortableTrait
{
    /**
     * @ORM\Column(type="smallint")
     *
     * @Assert\GreaterThanOrEqual(-32768)
     * @Assert\LessThanOrEqual(32767)
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
