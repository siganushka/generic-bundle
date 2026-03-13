<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatableMessage;
use Twig\Environment;

trait EditTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}/edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Environment $twig, UrlGeneratorInterface $urlGenerator, string $_id): Response
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_UPDATE, $entity)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEntityForm($entity);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->runInTransaction($this->entityManager->flush(...));

            $metadata = $this->entityManager->getClassMetadata($entity::class);
            $identifier = $metadata->getFieldValue($entity, $metadata->getSingleIdentifierFieldName());

            $message = \sprintf('Entity %s updated successfully!', $entity::class);
            $this->addFlashMessage($request, 'success', new TranslatableMessage($message, ['%_id%' => $identifier]));

            $route = \sprintf('app_%s_index', $this->getControllerAlias());
            $url = $urlGenerator->generate($route, []);

            return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
        }

        $template = \sprintf('%s/form.html.twig', $this->getTemplateAlias());
        $content = $twig->render($template, ['form' => $form->createView()]);

        return new Response($content);
    }
}
