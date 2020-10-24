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
        $resolver->setRequired('parent');
        $resolver->setDefaults([
            'district_options' => [],
        ]);

        $resolver->setNormalizer('query_builder', function (Options $options) {
            $queryBuilder = $options['em']->getRepository($options['class'])
                ->createQueryBuilder('r')
                ->where('r.parent = :parent')
                ->setParameter('parent', $options['parent'])
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
