<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Sequence;

class TimestampSequenceGenerator implements SequenceGeneratorInterface
{
    /**
     * 生成时间缀标识.
     */
    public function generate(): string
    {
        return str_pad((string) microtime(true), 15, '0');
    }
}
