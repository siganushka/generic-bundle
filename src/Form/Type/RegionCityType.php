<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (FormInterface $form, ?RegionInterface $parent = null) use ($options) {
            $form->add('district', RegionDistrictType::class, array_merge([
                'parent' => $parent,
            ], $options['district_options']));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $form = $event->getForm()->getParent();
            $data = $event->getData();

            $formModifier($form, $data);
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $form = $event->getForm()->getParent();
            $data = $event->getForm()->getData();

            $formModifier($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('parent', null);
        $resolver->setDefault('district_options', []);

        $resolver->setAllowedTypes('parent', ['null', RegionInterface::class]);

        $resolver->setNormalizer('choices', function (Options $options) {
            return $options['parent'] ? $options['parent']->getChildren() : [];
        });
    }

    public function getParent()
    {
        return RegionType::class;
    }
}
