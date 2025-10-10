<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\QueryBuilderUtils;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;

class QueryBuilderUtilsTest extends TestCase
{
    public function testAll(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $queryBuilder = new QueryBuilder($entityManager);
        $queryBuilder->from(Foo::class, 'f')->select('f');

        $ret = QueryBuilderUtils::addDateRangeFilter($queryBuilder, null, null);
        static::assertInstanceOf(QueryBuilder::class, $ret);
        static::assertSame('SELECT f FROM Siganushka\GenericBundle\Tests\Fixtures\Foo f', $queryBuilder->getDQL());

        $now = new \DateTime();
        QueryBuilderUtils::addDateRangeFilter($queryBuilder, $now, null);
        static::assertSame('SELECT f FROM Siganushka\GenericBundle\Tests\Fixtures\Foo f WHERE f.createdAt >= :startAt', $queryBuilder->getDQL());

        QueryBuilderUtils::addDateRangeFilter($queryBuilder, null, $now);
        static::assertSame('SELECT f FROM Siganushka\GenericBundle\Tests\Fixtures\Foo f WHERE f.createdAt >= :startAt AND f.createdAt <= :endAt', $queryBuilder->getDQL());

        // Reseting DQL where part.
        $queryBuilder->resetDQLPart('where');

        QueryBuilderUtils::addEnabledFilter($queryBuilder, null);
        static::assertSame('SELECT f FROM Siganushka\GenericBundle\Tests\Fixtures\Foo f', $queryBuilder->getDQL());

        QueryBuilderUtils::addEnabledFilter($queryBuilder, true);
        static::assertSame('SELECT f FROM Siganushka\GenericBundle\Tests\Fixtures\Foo f WHERE f.enabled = :enabled', $queryBuilder->getDQL());
    }
}
