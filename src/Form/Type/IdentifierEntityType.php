<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Form\DataTransformer\EntityToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdentifierEntityType extends AbstractType
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
    }

    /**
     * @param array{ class: class-string, identifier_field: string } $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new EntityToIdentifierTransformer($this->managerRegistry, $options['class'], $options['identifier_field']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('class');
        $resolver->setDefault('identifier_field', 'identifier');

        $resolver->setAllowedTypes('class', 'string');
        $resolver->setAllowedTypes('identifier_field', 'string');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
