<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
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
                $this->types[$type::class] = $type;
            }
        }
    }

    public function __invoke(Request $request, EntityManagerInterface $entityManager, Environment $twig, FormFactoryInterface $factory): Response
    {
        if ($form = $this->createForm($request, $factory, $entityManager)) {
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

    private function createForm(Request $request, FormFactoryInterface $factory, EntityManagerInterface $entityManager): ?FormInterface
    {
        $class = $request->query->getString('class');
        if (!$type = $this->types[$class] ?? null) {
            return null;
        }

        $form = $factory->create($type::class);

        /** @var class-string|null */
        $dataClass = $form->getConfig()->getDataClass();
        if ($dataClass && $request->query->has('id')) {
            try {
                $data = $entityManager->find($dataClass, $request->query->get('id'));
                $form->setData($data);
            } catch (\Throwable) {
            }
        }

        if ($form->getConfig()->getOption('compound')
            && !$form->getConfig()->getOption('inherit_data')
            && !$form->getConfig()->hasOption('keep_as_list')) {
            return $form;
        }

        return $factory->create(FormType::class)
            ->add($type->getBlockPrefix(), $type::class)
        ;
    }
}
