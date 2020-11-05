<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Manager\RegionManagerInterface;
use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionProvinceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (?FormInterface $form, ?RegionInterface $parent = null) use ($options) {
            if (null === $form) {
                return;
            }

            $form->add('city', RegionCityType::class, array_merge([
                'parent' => $parent,
                'district_options' => $options['district_options'],
            ], $options['city_options']));
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
        $resolver->setDefault('city_options', []);
        $resolver->setDefault('district_options', []);

        $resolver->setRequired('region_manager');
        $resolver->setAllowedTypes('region_manager', RegionManagerInterface::class);

        $resolver->setNormalizer('choices', function (Options $options, $b) {
            return $options['region_manager']->getProvinces();
        });
    }

    public function getParent()
    {
        return RegionType::class;
    }
}
