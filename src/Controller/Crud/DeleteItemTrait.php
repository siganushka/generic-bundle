<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'DELETE')]
    public function deleteItem(string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
