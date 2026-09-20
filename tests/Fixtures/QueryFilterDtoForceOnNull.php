<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Attribute\QueryFilter;

class QueryFilterDtoForceOnNull
{
    public function __construct(
        #[QueryFilter(forceOnNull: true)]
        public readonly ?string $q = null,
    ) {
    }
}
