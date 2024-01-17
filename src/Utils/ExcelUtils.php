<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

class ExcelUtils
{
    /**
     * 生成 Excel 列名，例列 A/B/C/AA/ZZ 等，从 0 开始.
     *
     * Examples:
     *
     * 0 => A
     * 1 => B
     * 2 => C
     * 25 => Z
     * 26 => AA
     * 27 => AB
     * 28 => AC
     *
     * @param int $number 数值列序号
     *
     * @return string 列名
     */
    public static function generateColumnDimension(int $number): string
    {
        if ($number < 0) {
            throw new \InvalidArgumentException('The number cannot be less than zero.');
        }

        $points = [];
        while (true) {
            if ($number < 26) {
                $points[] = $number;
                break;
            } else {
                $number -= 26;
                $points[] = 0;
            }
        }

        return implode('', array_map(fn (int $point) => \chr(65 + $point), $points));
    }
}
