<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class Html5ValidationTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['required'] = false;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
