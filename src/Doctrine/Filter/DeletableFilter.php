<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Siganushka\Contracts\Doctrine\DeletableInterface;

class DeletableFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        /*
         * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.5/reference/filters.html
         */
        if ($targetEntity->reflClass?->implementsInterface(DeletableInterface::class)) {
            return \sprintf('%s.deleted_at IS NULL', $targetTableAlias);
        }

        return '';
    }
}
