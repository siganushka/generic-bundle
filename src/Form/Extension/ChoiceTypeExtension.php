<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('placeholder', 'generic.choice');
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ChoiceType::class,
        ];
    }
}
