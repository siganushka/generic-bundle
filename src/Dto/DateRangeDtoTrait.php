<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

trait DateRangeDtoTrait
{
    public ?\DateTimeInterface $startAt = null;

    public ?\DateTimeInterface $endAt = null;
}
