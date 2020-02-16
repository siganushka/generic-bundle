<?php

namespace Siganushka\GenericBundle\Utils;

class CurrencyUtils
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'divisor' => 100,
            'decimals' => 2,
            'dec_point' => '.',
            'thousands_sep' => ',',
        ], $options);
    }

    /**
     * 格式化货币，单位：分.
     *
     * @param int|null $amount   货币金额
     * @param int      $decimals 小数位数
     * @param int      $divisor  进位模式
     *
     * @return string
     */
    public function format(?int $amount)
    {
        $value = (null === $amount) ? 0 : ($amount / $this->options['divisor']);

        return number_format($value,
            $this->options['decimals'],
            $this->options['dec_point'],
            $this->options['thousands_sep']);
    }
}
