<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

interface SortableInterface
{
    public const DEFAULT_SORT = 0;

    public function getSort(): ?int;

    public function setSort(?int $sort);
}
