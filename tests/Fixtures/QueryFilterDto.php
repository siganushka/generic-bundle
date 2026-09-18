<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Doctrine\Common\Collections\Expr\Comparison;
use Siganushka\GenericBundle\Attribute\QueryFilter;
use Siganushka\GenericBundle\Dto\DateRangeDto;

class QueryFilterDto
{
    public function __construct(
        #[QueryFilter]
        public readonly ?string $q1 = null,
        #[QueryFilter(expr: Comparison::NEQ)]
        public readonly ?int $q2 = null,
        #[QueryFilter(field: 'q333', expr: Comparison::LT)]
        public readonly ?bool $q3 = null,
        #[QueryFilter(expr: Comparison::LTE)]
        public readonly ?string $q4 = null,
        #[QueryFilter(expr: Comparison::GT)]
        public readonly ?string $q5 = null,
        #[QueryFilter(expr: Comparison::GTE)]
        public readonly ?string $q6 = null,
        #[QueryFilter(expr: Comparison::IN)]
        public readonly ?array $q7 = null,
        #[QueryFilter(expr: Comparison::NIN)]
        public readonly ?array $q8 = null,
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $q9 = null,
        #[QueryFilter(expr: Comparison::MEMBER_OF)]
        public readonly ?string $q10 = null,
        #[QueryFilter(expr: Comparison::STARTS_WITH)]
        public readonly ?string $q11 = null,
        #[QueryFilter(expr: Comparison::ENDS_WITH)]
        public readonly ?string $q12 = null,
        #[QueryFilter(expr: ['startAt' => Comparison::GTE, 'endAt' => Comparison::LTE])]
        public readonly ?DateRangeDto $q13 = null,
        #[QueryFilter(when: [self::class, 'q1IsNotNull'])]
        public readonly ?string $q14 = null,
    ) {
    }

    public static function q1IsNotNull(self $dto): bool
    {
        return null !== $dto->q1;
    }
}
