<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Identifier;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;

class SequenceGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new SequenceGenerator();
        $identifier = $generator->generate();

        static::assertNotEmpty($identifier);
        static::assertSame(16, mb_strlen($identifier));

        $y = mb_substr($identifier, 0, 2);
        $z = mb_substr($identifier, 2, 3);
        $s = mb_substr($identifier, 5, 5);

        $now = new \DateTimeImmutable();
        $today = $now->modify('today');

        static::assertSame($y, $now->format('y'));
        static::assertSame($z, $now->format('z'));
        static::assertSame($s, sprintf('%05s', $now->getTimestamp() - $today->getTimestamp()));
    }

    // public function testPerformance(): void
    // {
    //     $generator = new SequenceGenerator();
    //     $time_pre = microtime(true);

    //     $count = 1000000;
    //     $error = 0;

    //     $values = [];
    //     for ($i = 0; $i < $count; ++$i) {
    //         $v = $generator->generate();
    //         if (isset($values[$v])) {
    //             $error ++;
    //             continue;
    //         }

    //         $values[$v] = true;
    //     }

    //     $time_post = microtime(true);
    //     $exec_time = $time_post - $time_pre;

    //     dd(sprintf('生成 %d，成功 %d，重复 %d，耗时 %s', $count, count($values), $error, $exec_time));
    // }
}
