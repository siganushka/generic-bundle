<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

trait PutItemTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, SerializerInterface $serializer, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $form = $this->createEntityForm($entity);
        $form->submit($request->getPayload()->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return new JsonResponse($serializer->serialize($form, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, json: true);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $data = $serializer->serialize($entity, 'json', $this->serializationItemContext);

        return new JsonResponse($data, json: true);
    }
}
