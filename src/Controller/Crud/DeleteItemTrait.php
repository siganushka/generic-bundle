<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait DeleteItemTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'DELETE')]
    public function deleteItem(string $_id): Response
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_DELETE, $entity)) {
            throw new AccessDeniedException();
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
