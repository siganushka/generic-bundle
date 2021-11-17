<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Identifier;

class SequenceIdentifierGenerator implements IdentifierGeneratorInterface
{
    /**
     * 生成序列编号，单机下理论重复概率为百万分之一秒，为保证最小长度和尽可能唯一
     *
     * 生成特征：
     *
     * - 纯数字
     * - 固定长度（16 位）
     * - 有先后顺序（可排序）
     * - 更直观的体现时间范围（可反向解开对应时间）
     * - 非自增长（避免被采集或暴露业务量）
     *
     * 生成规律：
     *
     * 1-2 位：年份，比如 19 代表 2019 年
     * 3-5 位：一年中的第几天，比如 016 就是一年中的第 16 天，最大值为 365
     * 6-10 位：第一天中的秒数，从 0 开始，最大 86400
     * 11-16 位：当前系统时间的微秒数（百万分之一秒）
     */
    public function generate(): string
    {
        $current = new \DateTime();
        $midnight = new \DateTime('midnight');

        $y = $current->format('y');
        $z = $current->format('z');
        $s = $current->getTimestamp() - $midnight->getTimestamp();
        $u = $current->format('u');

        return sprintf('%02s%03s%05s%06s', $y, $z, $s, $u);
    }
}
