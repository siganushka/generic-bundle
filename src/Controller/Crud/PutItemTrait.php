<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

trait PutItemTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, SerializerInterface $serializer, string $_id): JsonResponse
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_UPDATE, $entity)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEntityForm($entity);
        $form->submit($request->getPayload()->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return new JsonResponse($serializer->serialize($form, 'json'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY, json: true);
        }

        $this->runInTransaction($this->entityManager->flush(...));

        $json = $serializer->serialize($entity, 'json', $this->serializationItemContext);

        return new JsonResponse($json, json: true);
    }
}
