<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Attribute\QueryFilter;

class QueryFilterDto3
{
    public function __construct(
        #[QueryFilter(when: [self::class, 'nonValidCallable'])]
        public readonly ?string $q = null,
    ) {
    }
}
