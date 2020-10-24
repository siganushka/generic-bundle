<?php

namespace Siganushka\GenericBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Model\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private $entityManager;
    private $dispatcher;
    private $normalizer;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher, NormalizerInterface $normalizer)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->normalizer = $normalizer;
    }

    public function __invoke(Request $request)
    {
        $queryBuilder = $this->entityManager->getRepository(Region::class)
            ->createQueryBuilder('r')
            ->where('r.parent IS null')
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

        $event = new RegionFilterEvent($regions);
        $this->dispatcher->dispatch($event);

        $data = $this->normalizer->normalize($event->getRegions(), null, [
            AbstractNormalizer::ATTRIBUTES => ['code', 'name'],
        ]);

        return new JsonResponse($data);
    }
}
