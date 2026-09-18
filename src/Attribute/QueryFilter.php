<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Attribute;

use Doctrine\ORM\Query\Expr\Comparison;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class QueryFilter
{
    /**
     * @param string|null              $field Define entity field name
     * @param string|array             $expr  Define comparison expressions
     * @param \Closure|array|null|null $when  define whether the comparison expressions applies to the query
     */
    public function __construct(
        public readonly ?string $field = null,
        public readonly string|array $expr = Comparison::EQ,
        public readonly \Closure|array|null $when = null,
    ) {
    }
}
