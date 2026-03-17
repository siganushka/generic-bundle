<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use OperationsTrait;

    #[Route(methods: 'GET')]
    public function getCollection(Request $request, SerializerInterface $serializer, PaginatorInterface $paginator): JsonResponse
    {
        $dto = $this->queryDtoClass && $serializer instanceof DenormalizerInterface
            ? $serializer->denormalize($request->query->all(), $this->queryDtoClass, 'csv')
            : null;

        $queryBuilder = $this->createEntityQueryBuilder('entity', $dto);
        $query = $queryBuilder->getQuery();

        $data = $this->paginationUsed
            ? $paginator->paginate($query)
            : $query->getResult();

        $json = $serializer->serialize($data, 'json', $this->serializationCollectionContext);

        return new JsonResponse($json, json: true);
    }
}
