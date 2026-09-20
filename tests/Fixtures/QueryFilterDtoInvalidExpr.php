<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Attribute\QueryFilter;

class QueryFilterDtoInvalidExpr
{
    public function __construct(
        #[QueryFilter(expr: 'INVALID')]
        public readonly ?string $q = null,
    ) {
    }
}
