<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\SortableInterface;

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
        /** @var EntityManagerInterface|null */
        $manager = $registry->getManagerForClass($entityClass);

        if (null === $manager) {
            throw new \LogicException(\sprintf('Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.', $entityClass));
        }

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }

    public function createQueryBuilderWithOrderBy(string $alias, ?string $indexBy = null, string $orderBy = 'DESC'): QueryBuilder
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);

        if (is_subclass_of($this->getEntityName(), SortableInterface::class)) {
            $queryBuilder->addOrderBy(\sprintf('%s.sort', $alias), $orderBy);
        }

        if (is_subclass_of($this->getEntityName(), ResourceInterface::class)) {
            $queryBuilder->addOrderBy(\sprintf('%s.id', $alias), $orderBy);
        }

        return $queryBuilder;
    }

    /**
     * @param mixed ...$args
     *
     * @return T
     */
    public function createNew(...$args): object
    {
        return (new \ReflectionClass($this->getEntityName()))->newInstanceArgs($args);
    }
}
