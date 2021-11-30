<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\CurrencyUtils;

class CurrencyUtilsTest extends TestCase
{
    /**
     * @dataProvider getCentsOfCurrencies
     */
    public function testDefaultOptions(?int $currency, string $formattedCurrency)
    {
        $formatter = new CurrencyUtils();

        var_dump('AAAAAAAAAAAA', $formatter->format(2147483647));
        var_dump('AAAAAAAAAAAA222', $formatter->format(2147483646));
        var_dump('AAAAAAAAAAAA222', $formatter->format(2147483645));
        var_dump('BBBBBBBBBBBB', $formatter->format(65536));
        var_dump('CCCCCCCCCCCC', $formatter->format(1024));

        var_dump('ZZZZZZZZZZZZ', number_format(2147483647 / 100, 2));
        static::assertSame($formattedCurrency, $formatter->format($currency));
    }

    /**
     * @dataProvider getCurrencies
     */
    public function testCustomOptions(?int $currency, string $formattedCurrency)
    {
        $formatter = new CurrencyUtils(0, false, null, 1);
        static::assertSame($formattedCurrency, $formatter->format($currency));
    }

    public function getCentsOfCurrencies()
    {
        return [
            [null, '0.00'],
            [0, '0.00'],
            [-1, '-0.01'],
            [-128, '-1.28'],
            [100, '1.00'],
            [65535, '655.35'],
            [2147483647, '21,474,836.47'],
        ];
    }

    public function getCurrencies()
    {
        return [
            [null, '0'],
            [0, '0'],
            [-1, '-1'],
            [-128, '-128'],
            [100, '100'],
            [65535, '65535'],
            [2147483647, '2147483647'],
        ];
    }
}
