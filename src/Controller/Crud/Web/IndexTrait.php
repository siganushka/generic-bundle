<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

trait IndexTrait
{
    use WebOperationsTrait;

    #[Route(methods: 'GET')]
    public function index(Request $request, SerializerInterface $serializer, Environment $twig, PaginatorInterface $paginator): Response
    {
        $dto = $this->queryDtoClass && $serializer instanceof DenormalizerInterface
            ? $serializer->denormalize($request->query->all(), $this->queryDtoClass, 'csv')
            : null;

        $queryBuilder = $this->createEntityQueryBuilder('entity', $dto);
        $query = $queryBuilder->getQuery();

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
