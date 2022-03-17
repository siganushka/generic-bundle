<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\CurrencyUtils;

class CurrencyUtilsTest extends TestCase
{
    // public function testPerformance(): void
    // {
    //     $formatter = new CurrencyUtils(2, '.', ',', 100);
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
    public function testDefaultOptions(?int $currency, string $formattedCurrency): void
    {
        $formatter = new CurrencyUtils(2, '.', ',', 100);
        static::assertSame($formattedCurrency, $formatter->format($currency));
    }

    /**
     * @dataProvider getCurrencies
     */
    public function testCustomOptions(?int $currency, string $formattedCurrency): void
    {
        $formatter = new CurrencyUtils(0, '.', ',', 1);
        static::assertSame($formattedCurrency, $formatter->format($currency));
    }

    /**
     * @return array<int, array<int, int|string|null>>
     */
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
        ];
    }

    /**
     * @return array<int, array<int, int|string|null>>
     */
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
        ];
    }
}
