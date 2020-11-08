<?php

namespace Siganushka\GenericBundle\Entity;

interface SortableInterface
{
    const DEFAULT_SORT = 255;

    public function getSort(): ?int;

    public function setSort(int $sort);
}
