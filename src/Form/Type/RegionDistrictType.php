<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Entity\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionDistrictType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_label' => 'name',
            'parent' => null,
        ]);

        $resolver->setAllowedTypes('parent', ['null', RegionInterface::class]);

        $resolver->setNormalizer('choices', function (Options $options) {
            return $options['parent'] ? $options['parent']->getChildren()->toArray() : [];
        });
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
