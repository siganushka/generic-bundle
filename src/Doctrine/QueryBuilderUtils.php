<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Doctrine;

use Doctrine\ORM\QueryBuilder;

class QueryBuilderUtils
{
    public static function addDateRangeFilter(QueryBuilder $queryBuilder, ?\DateTimeInterface $startAt, ?\DateTimeInterface $endAt): QueryBuilder
    {
        $alias = $queryBuilder->getAllAliases()[0];

        $startAt && $queryBuilder->andWhere(\sprintf('%s.createdAt >= :startAt', $alias))->setParameter('startAt', $startAt);
        $endAt && $queryBuilder->andWhere(\sprintf('%s.createdAt <= :endAt', $alias))->setParameter('endAt', $endAt);

        return $queryBuilder;
    }

    public static function addEnabledFilter(QueryBuilder $queryBuilder, ?bool $enabled): QueryBuilder
    {
        $alias = $queryBuilder->getAllAliases()[0];

        if (\is_bool($enabled)) {
            $queryBuilder->andWhere(\sprintf('%s.enabled = :enabled', $alias))->setParameter('enabled', $enabled);
        }

        return $queryBuilder;
    }
}
