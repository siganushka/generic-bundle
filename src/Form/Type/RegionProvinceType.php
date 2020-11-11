<?php

namespace Siganushka\GenericBundle\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Entity\Region;
use Siganushka\GenericBundle\Entity\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionProvinceType extends AbstractType
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

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
        $repository = $this->managerRegistry->getRepository(Region::class);
        $choices = $repository->findBy(['parent' => null], ['parent' => 'ASC', 'id' => 'ASC']);

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_value' => 'code',
            'choice_label' => 'name',
            'city_options' => [],
            'district_options' => [],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
