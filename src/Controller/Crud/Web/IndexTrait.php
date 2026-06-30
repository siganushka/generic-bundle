<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Twig\Environment;

trait IndexTrait
{
    use WebOperationsTrait;

    #[Route(methods: 'GET')]
    public function index(Request $request, DenormalizerInterface $denormalizer, Environment $twig, PaginatorInterface $paginator): Response
    {
        $arguments = [];
        if ($this->queryDtoClass) {
            $arguments[] = $denormalizer->denormalize($request->query->all(), $this->queryDtoClass, 'csv');
        }

        $qb = $this->createEntityQueryBuilder(...$arguments);
        $query = $qb->getQuery();

        if ($this->paginationUsed) {
            $context['pagination'] = $paginator->paginate($query);
        } else {
            $context['items'] = $query->getResult();
        }

        $template = \sprintf('%s/%s.html.twig', $this->getTemplateAlias(), __FUNCTION__);
        $content = $twig->render($template, $context);

        return new Response($content);
    }
}
