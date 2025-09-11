<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class PageQueryDto
{
    public function __construct(
        #[Positive]
        public readonly int $page = 1,
        #[Positive]
        #[Range(max: 100)]
        public readonly int $size = 10,
    ) {
    }
}
