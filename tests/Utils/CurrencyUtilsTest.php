<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\CurrencyUtils;

class CurrencyUtilsTest extends TestCase
{
    // public function testPerformance(): void
    // {
    //     $formatter = new CurrencyUtils();
    //     $time_pre = microtime(true);

    //     for ($i = -10; $i < 10000; $i ++) {
    //         $v = $formatter->format($i);
    //         // dump($i, $v);
    //     }

    //     $time_post = microtime(true);
    //     $exec_time = $time_post - $time_pre;
    //     dd($exec_time);
    // }

    /**
     * @dataProvider getCentsOfCurrencies
     */
    public function testDefaultOptions(?int $currency, string $formattedCurrency, array $context = []): void
    {
        $formatter = new CurrencyUtils();
        static::assertSame($formattedCurrency, $formatter->format($currency, $context));
    }

    /**
     * @dataProvider getCurrencies
     */
    public function testCustomOptions(?int $currency, string $formattedCurrency, array $context = []): void
    {
        $defaultContext = [
            CurrencyUtils::DIVISOR => 1,
            CurrencyUtils::DECIMALS => 0,
        ];

        $formatter = new CurrencyUtils($defaultContext);
        static::assertSame($formattedCurrency, $formatter->format($currency, $context));
    }

    public function getCentsOfCurrencies(): array
    {
        return [
            [null, '0.00'],
            [0, '0.00'],
            [-1, '-0.01'],
            [-128, '-1.28'],
            [100, '1.00'],
            [65535, '655.35'],
            [2147483647, '21,474,836.47'],
            [2147483647, '21474836.47', [CurrencyUtils::THOUSANDS_SEP => '']],
        ];
    }

    public function getCurrencies(): array
    {
        return [
            [null, '0'],
            [0, '0'],
            [-1, '-1'],
            [-128, '-128'],
            [100, '100'],
            [65535, '65,535'],
            [2147483647, '2,147,483,647'],
            [2147483647, '2147483647', [CurrencyUtils::THOUSANDS_SEP => '']],
        ];
    }
}
