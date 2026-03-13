<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

trait GetItemTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function getItem(SerializerInterface $serializer, string $_id): JsonResponse
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_READ, $entity)) {
            throw new AccessDeniedException();
        }

        $json = $serializer->serialize($entity, 'json', $this->serializationItemContext);

        return new JsonResponse($json, json: true);
    }
}
