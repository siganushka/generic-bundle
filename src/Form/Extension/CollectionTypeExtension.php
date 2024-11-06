<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['controller_name'] && ($options['allow_add'] || $options['allow_delete'])) {
            $view->vars['attr']['data-controller'] = $options['controller_name'];
            $view->vars['attr']['data-prototype-name'] = $options['prototype_name'];
            $view->vars['attr']['data-index'] = $form->count();
        }
    }

    /**
     * @phpstan-param array{
     *  allow_add: bool,
     *  allow_delete: bool,
     *  controller_name: string|null,
     *  add_button_type: class-string<FormTypeInterface>,
     *  add_button_options: array,
     *  delete_button_type: class-string<FormTypeInterface>,
     *  delete_button_options: array,
     * } $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $factory = $form->getConfig()->getFormFactory();
        if ($options['controller_name'] && $options['allow_add']) {
            $button = $factory->createNamed('add', $options['add_button_type'], null, $options['add_button_options']);
            $view->vars['add_button'] = $button->setParent($form)->createView($view);
        }

        if ($options['controller_name'] && $options['allow_delete']) {
            $button = $factory->createNamed('delete', $options['delete_button_type'], null, $options['delete_button_options']);

            $prototype = $view->vars['prototype'] ?? null;
            if ($prototype instanceof FormView) {
                $prototype->vars['delete_button'] = $button->setParent($form)->createView($view);
            }

            foreach ($view as $entryView) {
                $entryView->vars['delete_button'] = $button->setParent($form)->createView($entryView);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'controller_name' => 'siganushka-generic-collection',
            'add_button_type' => ButtonType::class,
            'add_button_options' => [],
            'delete_button_type' => ButtonType::class,
            'delete_button_options' => [],
        ]);

        $resolver->setAllowedTypes('controller_name', ['null', 'string']);

        $resolver->setAllowedTypes('add_button_type', 'string');
        $resolver->setAllowedTypes('add_button_options', 'array');

        $resolver->setAllowedTypes('delete_button_type', 'string');
        $resolver->setAllowedTypes('delete_button_options', 'array');

        $resolver->setNormalizer('add_button_options', function (Options $options, array $value) {
            $value['block_prefix'] ??= 'collection_add_button';
            $value['label'] ??= 'generic.add';
            $value['attr']['data-action'] = \sprintf('click->%s#add', $options['controller_name']);

            return $value;
        });

        $resolver->setNormalizer('delete_button_options', function (Options $options, array $value) {
            $value['block_prefix'] ??= 'collection_delete_button';
            $value['label'] ??= 'generic.delete';
            $value['attr']['data-action'] = \sprintf('click->%s#delete', $options['controller_name']);

            return $value;
        });

        $resolver->setNormalizer('entry_options', function (Options $options, array $value) {
            $value['row_attr']["data-{$options['controller_name']}-target"] = 'entry';

            return $value;
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            CollectionType::class,
        ];
    }
}
