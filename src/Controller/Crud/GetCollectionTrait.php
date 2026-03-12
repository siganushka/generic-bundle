<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use OperationsTrait;

    #[Route(methods: 'GET')]
    public function getCollection(SerializerInterface $serializer, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->createEntityQueryBuilder('entity');
        $query = $queryBuilder->getQuery();

        $data = $this->paginationUsed
            ? $paginator->paginate($query)
            : $query->getResult();

        $json = $serializer->serialize($data, 'json', $this->serializationCollectionContext);

        return new JsonResponse($json, json: true);
    }
}
