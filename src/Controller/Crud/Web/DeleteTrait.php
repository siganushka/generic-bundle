<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatableMessage;

trait DeleteTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}/delete', methods: 'GET')]
    public function delete(Request $request, CsrfTokenManagerInterface $tokenManager, UrlGeneratorInterface $urlGenerator, string $_id): RedirectResponse
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_DELETE, $entity)) {
            throw new AccessDeniedException();
        }

        $token = new CsrfToken('delete'.$_id, $request->query->getString('_token'));
        if ($tokenManager->isTokenValid($token)) {
            $this->runInTransaction(static function (EntityManagerInterface $em) use ($entity): void {
                $em->remove($entity);
                $em->flush();
            });

            $metadata = $this->entityManager->getClassMetadata($entity::class);
            $identifier = $metadata->getFieldValue($entity, $metadata->getSingleIdentifierFieldName());

            $message = \sprintf('Entity %s deleted successfully!', $entity::class);
            $this->addFlashMessage($request, 'success', new TranslatableMessage($message, ['%_id%' => $identifier]));
        } else {
            $this->addFlashMessage($request, 'danger', new TranslatableMessage('Invalid csrf token.'));
        }

        $route = \sprintf('app_%s_index', $this->getControllerAlias());
        $url = $urlGenerator->generate($route, []);

        return new RedirectResponse($url, RedirectResponse::HTTP_SEE_OTHER);
    }
}
