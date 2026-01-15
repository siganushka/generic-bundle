<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Entity\Embeddable;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Entity\Embeddable\DateRange;

class DateRangeTest extends TestCase
{
    public function testAll(): void
    {
        $range = new DateRange();
        static::assertNull($range->getStartAt());
        static::assertNull($range->getEndAt());

        $startAt = new \DateTimeImmutable();
        $endAt = $startAt->modify('+3 days');

        $range->setStartAt($startAt);
        $range->setEndAt($endAt);
    }
}
