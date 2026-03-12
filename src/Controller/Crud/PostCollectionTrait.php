<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

trait PostCollectionTrait
{
    use OperationsTrait;

    #[Route(methods: 'POST')]
    public function postCollection(Request $request, SerializerInterface $serializer): Response
    {
        $entity = $this->createEntity();
        if (!$this->isGrantedForOperation(self::OPERATION_CREATE, $entity)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEntityForm($entity);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            return new JsonResponse($serializer->serialize($form, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, json: true);
        }

        $this->runInTransaction(function () use ($entity): void {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        });

        $data = $serializer->serialize($entity, 'json', $this->serializationItemContext);

        return new JsonResponse($data, Response::HTTP_CREATED, json: true);
    }
}
