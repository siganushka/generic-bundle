<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\GenericBundle\Attribute\QueryFilter;

/**
 * @see https://github.com/bmewburn/vscode-intelephense/issues/2447
 *
 * @template T of object
 * @template-extends EntityRepository<T>
 */
class GenericEntityRepository extends EntityRepository
{
    /**
     * @param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $manager = $registry->getManagerForClass($entityClass);

        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(\sprintf('Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.', $entityClass));
        }

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }

    public function createQueryBuilderWithOrderBy(string $alias, ?string $indexBy = null, \SortDirection|string $orderBy = \SortDirection::Descending): QueryBuilder
    {
        // Compatible with doctrine/orm > 3.7.0
        if ($orderBy instanceof \SortDirection) {
            $orderBy = \SortDirection::Ascending === $orderBy ? 'ASC' : 'DESC';
        }

        $qb = $this->createQueryBuilder($alias, $indexBy);

        if (is_subclass_of($this->getEntityName(), ResourceInterface::class)) {
            $qb->addOrderBy(\sprintf('%s.id', $alias), $orderBy);
        }

        return $qb;
    }

    /**
     * @return T
     */
    public function createNew(mixed ...$args): object
    {
        return (new \ReflectionClass($this->getEntityName()))->newInstanceArgs($args);
    }

    public static function createCriteriaFromDto(object $dto): Criteria
    {
        $expressions = [];
        $expressionFn = static fn (string $expr): \Closure => match ($expr) {
            Comparison::EQ => Criteria::expr()->eq(...),
            Comparison::NEQ => Criteria::expr()->neq(...),
            Comparison::LT => Criteria::expr()->lt(...),
            Comparison::LTE => Criteria::expr()->lte(...),
            Comparison::GT => Criteria::expr()->gt(...),
            Comparison::GTE => Criteria::expr()->gte(...),
            Comparison::IN => Criteria::expr()->in(...),
            Comparison::NIN => Criteria::expr()->notIn(...),
            Comparison::CONTAINS => Criteria::expr()->contains(...),
            Comparison::MEMBER_OF => Criteria::expr()->memberOf(...),
            Comparison::STARTS_WITH => Criteria::expr()->startsWith(...),
            Comparison::ENDS_WITH => Criteria::expr()->endsWith(...),
            default => throw new \InvalidArgumentException(\sprintf('Unsupported expr "%s".', $expr)),
        };

        $emptyFn = static fn (mixed $value): bool => null === $value || '' === $value || [] === $value;

        $ref = new \ReflectionClass($dto);
        foreach ($ref->getProperties() as $property) {
            if (!$property->isInitialized($dto)) {
                continue;
            }

            $value = $property->getValue($dto);
            if ($emptyFn($value)) {
                continue;
            }

            $attribute = $property->getAttributes(QueryFilter::class)[0] ?? null;
            if (null === $attribute) {
                continue;
            }

            $filter = $attribute->newInstance();
            if (\is_callable($filter->when) && !($filter->when)($dto, $value)) {
                continue;
            }

            if (\is_string($filter->expr)) {
                /* @phpstan-ignore argument.type */
                $expressions[] = $expressionFn($filter->expr)($filter->field ?? $property->getName(), $value);
            } else {
                foreach ($filter->expr as $subProperty => $subExpr) {
                    if (!$emptyFn($value->$subProperty)) {
                        $expressions[] = $expressionFn($subExpr)($filter->field ?? $property->getName(), $value->$subProperty);
                    }
                }
            }
        }

        return 0 === \count($expressions)
            ? Criteria::create()
            : Criteria::create()->andWhere(Criteria::expr()->andX(...$expressions));
    }
}
