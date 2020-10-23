<?php

namespace Siganushka\GenericBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class RegionController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
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

        $json = $this->serializer->serialize($regions, 'json', [
            JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE,
            AbstractNormalizer::ATTRIBUTES => ['name', 'code'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }
}
