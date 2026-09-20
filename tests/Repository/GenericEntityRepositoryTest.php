<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Repository;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\QueryExpressionVisitor;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\SortableTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\GenericBundle\Dto\DateRangeDto;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\GenericBundle\Tests\Fixtures\QueryFilterDto;
use Siganushka\GenericBundle\Tests\Fixtures\QueryFilterDtoForceOnNull;
use Siganushka\GenericBundle\Tests\Fixtures\QueryFilterDtoInvalidExpr;
use Siganushka\GenericBundle\Tests\Fixtures\QueryFilterDtoInvalidWhen;

class GenericEntityRepositoryTest extends TestCase
{
    public function testAll(): void
    {
        $repository = $this->createRepository(Foo::class);

        $entity = $repository->createNew('foo');
        static::assertInstanceOf(Foo::class, $entity);
        static::assertSame('foo', $entity->getArg1());
        static::assertSame(128, $entity->getArg2());

        $entity = $repository->createNew('bar', 256);
        static::assertInstanceOf(Foo::class, $entity);
        static::assertSame('bar', $entity->getArg1());
        static::assertSame(256, $entity->getArg2());

        $entity->setArg1('baz');
        $entity->setArg2(512);
        static::assertSame('baz', $entity->getArg1());
        static::assertSame(512, $entity->getArg2());

        static::assertSame(
            'SELECT f FROM Siganushka\GenericBundle\Tests\Repository\Foo f ORDER BY f.id DESC',
            $repository->createQueryBuilderWithOrderBy('f')->getDQL()
        );

        static::assertSame(
            'SELECT f FROM Siganushka\GenericBundle\Tests\Repository\Foo f ORDER BY f.id ASC',
            $repository->createQueryBuilderWithOrderBy('f', orderBy: \SortDirection::Ascending)->getDQL()
        );
    }

    #[DataProvider('queryFilterProvider')]
    public function testCreateCriteriaFromDto(object $dto, ?string $dql): void
    {
        $criteria = GenericEntityRepository::createCriteriaFromDto($dto);

        $whereExpression = $criteria->getWhereExpression();
        if (null === $dql) {
            static::assertNull($whereExpression);
        } else {
            static::assertInstanceOf(Expression::class, $whereExpression);

            $visitor = new QueryExpressionVisitor(['f']);

            $queryExpression = $visitor->dispatch($whereExpression);
            static::assertInstanceOf(\Stringable::class, $queryExpression);
            static::assertSame($dql, $queryExpression->__toString());
        }
    }

    public function testCreateCriteriaFromDtoWithExprException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The QueryFilter::expr with value "INVALID" is not supported');

        $dto = new QueryFilterDtoInvalidExpr(q: 'hello');
        GenericEntityRepository::createCriteriaFromDto($dto);
    }

    public function testCreateCriteriaFromDtoWithWhenException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The QueryFilter::when is not a callable expression');

        $dto = new QueryFilterDtoInvalidWhen(q: 'hello');
        GenericEntityRepository::createCriteriaFromDto($dto);
    }

    public function testCreateNewWithArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        $repository = $this->createRepository(Foo::class);
        $repository->createNew();
    }

    /**
     * @param class-string<Foo> $entityClass
     *
     * @return GenericEntityRepository<Foo>
     */
    private function createRepository(string $entityClass): GenericEntityRepository
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->method('getClassMetadata')
            ->willReturn(new ClassMetadata($entityClass))
        ;

        $entityManager->method('createQueryBuilder')
            // Using willReturnCallback to create new instance
            ->willReturnCallback(static fn () => new QueryBuilder($entityManager))
        ;

        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        return new GenericEntityRepository($managerRegistry, $entityClass);
    }

    public static function queryFilterProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        yield [
            new QueryFilterDto(
                q1: 'q1_value',
                q2: 0,
                q3: false,
                q4: 'q4_value',
                q5: 'q5_value',
                q6: 'q6_value',
                q7: ['q7_value'],
                q8: ['q8_value'],
                q9: 'q9_value',
                q10: 'q10_value',
                q11: 'q11_value',
                q12: 'q12_value',
                q13: new DateRangeDto($now, $now->modify('+7 days')),
                q14: 'q14_value',
            ),
            'f.q1 = :q1 AND f.q2 <> :q2 AND f.q333 < :q333 AND f.q4 <= :q4 AND f.q5 > :q5 AND f.q6 >= :q6 AND f.q7 IN(:q7) AND f.q8 NOT IN(:q8) AND f.q9 LIKE :q9 AND q10 MEMBER OF q10_value AND f.q11 LIKE :q11 AND f.q12 LIKE :q12 AND f.q13 >= :q13 AND f.q13 <= :q13_12 AND f.q14 = :q14',
        ];

        yield [
            new QueryFilterDto(q1: null),
            null,
        ];

        yield [
            new QueryFilterDto(q1: ''),
            null,
        ];

        yield [
            new QueryFilterDto(q7: []),
            null,
        ];

        yield [
            new QueryFilterDto(q13: new DateRangeDto()),
            null,
        ];

        yield [
            new QueryFilterDto(q13: new DateRangeDto(startAt: $now)),
            'f.q13 >= :q13',
        ];

        yield [
            new QueryFilterDto(q13: new DateRangeDto(endAt: $now)),
            'f.q13 <= :q13',
        ];

        yield [
            new QueryFilterDto(q14: 'q14_value'),
            null,
        ];

        yield [
            new QueryFilterDto(q1: 'q1_value', q14: 'q14_value'),
            'f.q1 = :q1 AND f.q14 = :q14',
        ];

        yield [
            new QueryFilterDtoForceOnNull(),
            'f.q IS NULL',
        ];

        yield [
            new QueryFilterDtoForceOnNull(q: 'q_value'),
            'f.q = :q',
        ];
    }
}

class Foo implements ResourceInterface, SortableInterface, TimestampableInterface
{
    use ResourceTrait;
    use SortableTrait;
    use TimestampableTrait;

    private ?string $arg1 = null;
    private ?int $arg2 = null;

    public function __construct(string $arg1, int $arg2 = 128)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function setArg1(string $arg1): self
    {
        $this->arg1 = $arg1;

        return $this;
    }

    public function getArg1(): ?string
    {
        return $this->arg1;
    }

    public function setArg2(int $arg2): self
    {
        $this->arg2 = $arg2;

        return $this;
    }

    public function getArg2(): ?int
    {
        return $this->arg2;
    }
}
