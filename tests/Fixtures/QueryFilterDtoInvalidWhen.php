<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Attribute\QueryFilter;

class QueryFilterDtoInvalidWhen
{
    public function __construct(
        #[QueryFilter(when: [self::class, 'INVALID'])]
        public readonly ?string $q = null,
    ) {
    }
}
