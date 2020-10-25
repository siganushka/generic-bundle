<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Model\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Region::class,
            'choice_label' => 'name',
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
