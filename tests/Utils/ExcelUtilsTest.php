<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\ExcelUtils;

class ExcelUtilsTest extends TestCase
{
    /**
     * @dataProvider columnDimensionProvider
     */
    public function testGenerateColumnDimension(int $number, string $columnDimension): void
    {
        static::assertSame($columnDimension, ExcelUtils::generateColumnDimension($number));
    }

    public static function columnDimensionProvider(): iterable
    {
        yield [0, 'A'];
        yield [1, 'B'];
        yield [2, 'C'];
        yield [25, 'Z'];
        yield [26, 'AA'];
        yield [27, 'AB'];
        yield [28, 'AC'];
    }
}
