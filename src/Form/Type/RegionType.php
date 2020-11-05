<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Manager\RegionManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    private $regionManager;

    public function __construct(RegionManagerInterface $regionManager)
    {
        $this->regionManager = $regionManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_label' => 'name',
            'region_manager' => $this->regionManager,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
