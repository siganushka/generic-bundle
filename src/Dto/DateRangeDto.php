<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

class DateRangeDto
{
    public function __construct(
        public readonly ?\DateTimeInterface $startAt = null,
        public readonly ?\DateTimeInterface $endAt = null,
    ) {
    }
}
