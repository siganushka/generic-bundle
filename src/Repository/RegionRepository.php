<?php

namespace Siganushka\GenericBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Model\Region;

class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function getProvinces(): array
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.parent IS null')
            ->addOrderBy('r.parent', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
