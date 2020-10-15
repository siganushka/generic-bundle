<?php

namespace Siganushka\GenericBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RegionController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request)
    {
        $queryBuilder = $this->entityManager->getRepository(Region::class)
            ->createQueryBuilder('r')
            ->where('r.parent IS null')
            ->addOrderBy('r.depth', 'ASC')
            ->addOrderBy('r.parent', 'ASC')
            ->addOrderBy('r.id', 'ASC')
        ;

        if ($request->query->has('parent')) {
            $queryBuilder
                ->where('r.parent = :parent')
                ->setParameter('parent', $request->query->get('parent'))
            ;
        }

        $query = $queryBuilder->getQuery();
        $regions = $query->getArrayResult();

        return new JsonResponse($regions);
    }
}
