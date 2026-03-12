<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

trait ShowTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function show(Environment $twig, string $_id): Response
    {
        $entity = $this->findEntity($_id);
        if (!$this->isGrantedForOperation(self::OPERATION_READ, $entity)) {
            throw new AccessDeniedException();
        }

        $template = \sprintf('%s/%s.html.twig', $this->getTemplateAlias(), __FUNCTION__);
        $content = $twig->render($template, compact('entity'));

        return new Response($content);
    }
}
