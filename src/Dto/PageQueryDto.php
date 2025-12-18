<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Positive;

class PageQueryDto
{
    public function __construct(
        #[Positive]
        public readonly int $page = 1,
        #[Positive]
        #[LessThanOrEqual(100)]
        public readonly int $size = 10,
    ) {
    }
}
