<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Siganushka\GenericBundle\Entity\AbstractNestable;

/**
 * @template T of AbstractNestable = AbstractNestable
 *
 * @extends GenericEntityRepository<T>
 */
class NestableRepository extends GenericEntityRepository
{
    /**
     * @return array<int, T>
     */
    public function findByParent(int|string|null $parent, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy(compact('parent'), $orderBy, $limit, $offset);
    }
}
