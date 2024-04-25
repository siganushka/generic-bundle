<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;

class GenericEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param string      $alias   the alias for the table
     * @param string|null $indexBy the index for the from
     */
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);

        if (is_subclass_of($this->_entityName, SortableInterface::class)) {
            $queryBuilder->addOrderBy(sprintf('%s.sort', $alias), 'DESC');
        }

        if (is_subclass_of($this->_entityName, TimestampableInterface::class)) {
            $queryBuilder->addOrderBy(sprintf('%s.createdAt', $alias), 'DESC');
        }

        if (is_subclass_of($this->_entityName, ResourceInterface::class)) {
            $queryBuilder->addOrderBy(sprintf('%s.id', $alias), 'DESC');
        }

        return $queryBuilder;
    }

    /**
     * @param mixed ...$args
     */
    public function createNew(...$args): ResourceInterface
    {
        return (new \ReflectionClass($this->_entityName))->newInstanceArgs($args);
    }
}
