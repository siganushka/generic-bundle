<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Repository;

use Siganushka\GenericBundle\Model\NestableInterface;

/**
 * @template TNode of NestableInterface = NestableInterface
 *
 * @extends GenericEntityRepository<TNode>
 */
class NestableRepository extends GenericEntityRepository
{
    /**
     * @return list<TNode>
     */
    public function findByParent(int|string|null $parent, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy(compact('parent'), $orderBy, $limit, $offset);
    }
}
