<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

/**
 * Currency utils.
 */
class CurrencyUtils
{
    public const DIVISOR = 'divisor';
    public const DECIMALS = 'decimals';
    public const DEC_POINT = 'dec_point';
    public const THOUSANDS_SEP = 'thousands_sep';

    private array $defaultContext = [
        self::DIVISOR => 100,
        self::DECIMALS => 2,
        self::DEC_POINT => '.',
        self::THOUSANDS_SEP => ',',
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    public function format(?int $number, array $context = []): string
    {
        // null to 0
        if (null === $number) {
            $number = 0;
        }

        $divisor = $context[self::DIVISOR] ?? $this->defaultContext[self::DIVISOR];
        if (1 !== $divisor) {
            $number /= $divisor;
        }

        return number_format(
            $number,
            $context[self::DECIMALS] ?? $this->defaultContext[self::DECIMALS],
            $context[self::DEC_POINT] ?? $this->defaultContext[self::DEC_POINT],
            $context[self::THOUSANDS_SEP] ?? $this->defaultContext[self::THOUSANDS_SEP],
        );
    }
}
