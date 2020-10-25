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

class RegionProvinceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (FormInterface $form, ?RegionInterface $parent = null) use ($options) {
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

        $resolver->setNormalizer('query_builder', function (Options $options) {
            $queryBuilder = $options['em']->getRepository($options['class'])
                ->createQueryBuilder('r')
                ->where('r.parent IS null')
                ->addOrderBy('r.parent', 'ASC')
                ->addOrderBy('r.id', 'ASC');

            return $queryBuilder;
        });
    }

    public function getParent()
    {
        return RegionType::class;
    }
}
