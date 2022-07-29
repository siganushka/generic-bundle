<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Extension\DisableHtml5Validation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryBuilder;

class DisableHtml5ValidationTest extends TestCase
{
    public function testDefault(): void
    {
        $formFactoryBuilder = new FormFactoryBuilder();

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(TextType::class)
            ->getForm()
        ;

        $view = $form->createView();
        static::assertTrue($view->vars['required']);
    }

    public function testDisableHtml5Validation(): void
    {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addTypeExtension(new DisableHtml5Validation());

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(TextType::class)
            ->getForm()
        ;

        $view = $form->createView();
        static::assertFalse($view->vars['required']);
    }
}
