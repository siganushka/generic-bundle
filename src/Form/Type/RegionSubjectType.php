<?php

namespace Siganushka\GenericBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionSubjectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Region::class,
            'choice_label' => 'name',
            'placeholder' => 'app.choice_empty',
            'label' => 'entity.region',
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('r')
                    ->where('r.parent IS NULL')
                    ->addOrderBy('r.parent', 'ASC')
                    ->addOrderBy('r.id', 'ASC');
            },
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
