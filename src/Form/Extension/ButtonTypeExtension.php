<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('priority', -128);
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ButtonType::class,
        ];
    }
}
