<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Dto;

class EnableDto
{
    public function __construct(public readonly ?bool $enabled = null)
    {
    }
}
