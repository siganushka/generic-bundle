<?php

namespace Siganushka\GenericBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;

class RegionManager implements RegionManagerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getProvinces(): array
    {
        $query = $this->entityManager->getRepository(Region::class)
            ->createQueryBuilder('r')
            ->where('r.parent IS null')
            ->addOrderBy('r.parent', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function getChildrenByParent(?RegionInterface $parent): array
    {
        return (null === $parent) ? [] : $parent->getChildren();
    }
}
