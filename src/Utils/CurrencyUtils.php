<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Currency utils.
 */
class CurrencyUtils
{
    protected $transformer;

    public function __construct(?int $scale = 2, ?bool $grouping = true, ?int $roundingMode = \NumberFormatter::ROUND_HALFUP, ?int $divisor = 100)
    {
        var_dump(sprintf('NumberFormatter VALUE: %s %d', gettype(\NumberFormatter::ROUND_HALFUP), \NumberFormatter::ROUND_HALFUP));


        $this->transformer = new MoneyToLocalizedStringTransformer($scale, $grouping, $roundingMode, $divisor);
    }

    public function format(?int $num): string
    {
        // null to 0
        if (null === $num) {
            $num = 0;
        }

        return $this->transformer->transform($num);
    }
}
