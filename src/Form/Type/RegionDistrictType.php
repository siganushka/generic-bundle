<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionDistrictType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('parent', null);
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
