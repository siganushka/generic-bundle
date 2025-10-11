<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

trait PageQueryDtoTrait
{
    #[Positive]
    public int $page = 1;

    #[Positive]
    #[Range(max: 100)]
    public int $size = 10;
}
