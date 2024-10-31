<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
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
            // Controller name for "@hotwired/stimulus"
            'controller_name' => 'siganushka-generic-collection',
            // Configure add button
            'add_button_type' => ButtonType::class,
            'add_button_options' => [],
            // Configure delete button
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
