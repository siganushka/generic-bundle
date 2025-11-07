<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FormController
{
    /**
     * @var array<string, FormTypeInterface>
     */
    private array $types;

    /**
     * @param iterable<int, FormTypeInterface> $formTypes
     */
    public function __construct(iterable $formTypes)
    {
        foreach ($formTypes as $type) {
            if (!str_starts_with($type::class, 'Symfony\\')) {
                $this->types[$type->getBlockPrefix()] = $type;
            }
        }
    }

    public function __invoke(Request $request, Environment $twig, FormFactoryInterface $factory): Response
    {
        if ($form = $this->createForm($request, $factory)) {
            $form->add('submit', SubmitType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                dd(__METHOD__, $form->getData());
            }
        }

        $status = $request->isMethod('POST')
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        $content = $twig->render('@SiganushkaGeneric/form.html.twig', [
            'types' => $this->types,
            'form' => $form?->createView(),
        ]);

        return new Response($content, $status);
    }

    private function createForm(Request $request, FormFactoryInterface $factory): ?FormInterface
    {
        $alias = $request->query->getString('alias');
        if (!$type = $this->types[$alias] ?? null) {
            return null;
        }

        $form = $factory->create($type::class);
        if ($form->getConfig()->getOption('compound') && CollectionType::class !== $type->getParent()) {
            return $form;
        }

        return $factory->create(FormType::class)
            ->add($type->getBlockPrefix(), $type::class)
        ;
    }
}
