<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Positive;

trait PageQueryDtoTrait
{
    #[Positive]
    public int $page = 1;

    #[Positive]
    #[LessThanOrEqual(100)]
    public int $size = 10;
}
