<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\Contracts\Doctrine\ResourceInterface;

class GenericEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        if (!is_subclass_of($entityClass, ResourceInterface::class)) {
            throw new \UnexpectedValueException(sprintf('Expected argument of type "%s", "%s" given', ResourceInterface::class, $entityClass));
        }

        parent::__construct($registry, $entityClass);
    }

    public function createNew(...$args): ResourceInterface
    {
        $ref = new \ReflectionClass($this->_entityName);

        /** @var ResourceInterface */
        $entity = \count($args)
            ? $ref->newInstance(...$args)
            : $ref->newInstanceWithoutConstructor();

        return $entity;
    }

    public function add(ResourceInterface $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(ResourceInterface $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }
}
