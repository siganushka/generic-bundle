<?php

namespace Siganushka\GenericBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('province', RegionProvinceType::class, array_merge([
            'city_options' => $options['city_options'],
            'district_options' => $options['district_options'],
        ], $options['province_options']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'inherit_data' => true,
            'province_options' => [],
            'city_options' => [],
            'district_options' => [],
        ]);
    }
}
