<?php

namespace Siganushka\GenericBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GenericExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('str_repeat', [$this, 'strRepeat']),
        ];
    }

    public function strRepeat(string $input, int $multiplier)
    {
        return str_repeat($input, $multiplier);
    }
}
