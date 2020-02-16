<?php

namespace Siganushka\GenericBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GenericExtension extends AbstractExtension
{
    private $currencyUtils;

    public function __construct(CurrencyUtils $currencyUtils)
    {
        $this->currencyUtils = $currencyUtils;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter']),
            new TwigFilter('str_repeat', [$this, 'strRepeat']),
        ];
    }

    public function priceFilter(int $amount)
    {
        return $this->currencyUtils->format($amount);
    }

    public function strRepeat(string $input, int $multiplier)
    {
        return str_repeat($input, $multiplier);
    }
}
