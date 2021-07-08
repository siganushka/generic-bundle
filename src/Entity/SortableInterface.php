<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

interface SortableInterface
{
    public const DEFAULT_SORTED = 0;

    public function getSorted(): ?int;

    public function setSorted(?int $sorted);
}
