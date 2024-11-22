<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\TimestampableInterface;

/**
 * @template T of object
 * @template-extends EntityRepository<T>
 *
 * @method T|null find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null)
 * @method T|null findOneBy(array $criteria, array|null $orderBy = null): object|null
 * @method T[] findAll()
 * @method T[] findBy(array $criteria, array|null $orderBy = null, int|null $limit = null, int|null $offset = null)
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

    public function createQueryBuilder(string $alias, string $indexBy = null): QueryBuilder
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);

        if (is_subclass_of($this->getEntityName(), SortableInterface::class)) {
            $queryBuilder->addOrderBy(\sprintf('%s.sort', $alias), 'DESC');
        }

        if (is_subclass_of($this->getEntityName(), TimestampableInterface::class)) {
            $queryBuilder->addOrderBy(\sprintf('%s.createdAt', $alias), 'DESC');
        }

        if (is_subclass_of($this->getEntityName(), ResourceInterface::class)) {
            $queryBuilder->addOrderBy(\sprintf('%s.id', $alias), 'DESC');
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
