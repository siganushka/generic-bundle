<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\ExcelUtils;

class ExcelUtilsTest extends TestCase
{
    /**
     * @dataProvider getColumnDimension
     */
    public function testGenerateColumnDimension(int $number, string $columnDimension): void
    {
        static::assertSame($columnDimension, ExcelUtils::generateColumnDimension($number));
    }

    public function getColumnDimension(): array
    {
        return [
            [0, 'A'],
            [1, 'B'],
            [2, 'C'],
            [25, 'Z'],
            [26, 'AA'],
            [27, 'AB'],
            [28, 'AC'],
        ];
    }
}
