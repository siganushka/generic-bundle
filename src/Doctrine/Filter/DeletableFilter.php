<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\GenericBundle\Doctrine\EventListener\DeletableListener;

class DeletableFilter extends SQLFilter
{
    /**
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.5/reference/filters.html
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if ($targetEntity->reflClass?->implementsInterface(DeletableInterface::class)) {
            return \sprintf('%s.%s = 0', $targetTableAlias, DeletableListener::FIELD_NAME);
        }

        return '';
    }
}
