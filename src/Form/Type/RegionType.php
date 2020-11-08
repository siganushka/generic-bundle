<?php

namespace Siganushka\GenericBundle\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_label' => 'name',
            'region_repository' => $this->managerRegistry->getRepository(Region::class),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
