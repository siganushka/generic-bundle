<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

/**
 * Currency utils.
 */
class CurrencyUtils
{
    private int $decimals;
    private string $decPoint;
    private string $thousandsSep;
    private int $divisor;

    public function __construct(int $decimals, string $decPoint, string $thousandsSep, int $divisor)
    {
        $this->decimals = $decimals;
        $this->decPoint = $decPoint;
        $this->thousandsSep = $thousandsSep;
        $this->divisor = $divisor;
    }

    public function format(?int $number): string
    {
        // null to 0
        if (null === $number) {
            $number = 0;
        }

        if (1 !== $this->divisor) {
            $number /= $this->divisor;
        }

        return number_format($number, $this->decimals, $this->decPoint, $this->thousandsSep);
    }
}
