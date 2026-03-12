<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
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
    public function delete(Request $request, CsrfTokenManagerInterface $tokenManager, UrlGeneratorInterface $urlGenerator, string $_id): Response
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_DELETE, $entity)) {
            throw new AccessDeniedException();
        }

        $token = new CsrfToken('delete'.$_id, $request->query->getString('_token'));
        if ($tokenManager->isTokenValid($token)) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            $metadata = $this->entityManager->getClassMetadata($entity::class);
            $identifier = $metadata->getFieldValue($entity, $metadata->getSingleIdentifierFieldName());

            $type = 'success';
            $message = new TranslatableMessage(\sprintf('Entity %s deleted successfully!', $entity::class), ['%_id%' => $identifier]);
        } else {
            $type = 'danger';
            $message = new TranslatableMessage('Invalid csrf token.');
        }

        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add($type, $message);
        }

        $route = \sprintf('app_%s_index', $this->getControllerAlias());
        $url = $urlGenerator->generate($route, []);

        return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
    }
}
