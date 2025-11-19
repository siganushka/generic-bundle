<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsrfTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $stateless = $request && $request->attributes->getBoolean('_stateless', false);

        $resolver->setDefault('csrf_protection', !$stateless);
        $resolver->setDefault('allow_extra_fields', $stateless);
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            FormType::class,
        ];
    }
}
