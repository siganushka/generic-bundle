<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatableMessage;
use Twig\Environment;

trait NewTrait
{
    use WebOperationsTrait;

    #[Route('/new', methods: ['GET', 'POST'])]
    public function new(Request $request, Environment $twig, UrlGeneratorInterface $urlGenerator): Response
    {
        $entity = $this->createEntity();
        if (!$this->isGrantedForOperation(self::OPERATION_CREATE, $entity)) {
            throw new AccessDeniedException();
        }

        $form = $this->createEntityForm($entity);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->runInTransaction(function () use ($entity): void {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            });

            $session = $request->getSession();
            if ($session instanceof FlashBagAwareSessionInterface) {
                $metadata = $this->entityManager->getClassMetadata($entity::class);
                $session->getFlashBag()->add('success', new TranslatableMessage(
                    \sprintf('Entity %s created successfully!', $entity::class),
                    ['%_id%' => $metadata->getFieldValue($entity, $metadata->getSingleIdentifierFieldName())],
                ));
            }

            $route = \sprintf('app_%s_index', $this->getControllerAlias());
            $url = $urlGenerator->generate($route, []);

            return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
        }

        $template = \sprintf('%s/form.html.twig', $this->getTemplateAlias());
        $content = $twig->render($template, ['form' => $form->createView()]);

        return new Response($content);
    }
}
