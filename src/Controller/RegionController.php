<?php

namespace Siganushka\GenericBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegionController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->getRepository(Region::class)
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
        $regions = $query->getResult();

        return $this->json($regions);
    }
}
