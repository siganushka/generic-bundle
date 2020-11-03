<?php

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Extension\DisableHtml5ValidateTypeExtension;
use Symfony\Component\Form\FormFactoryBuilder;

class DisableHtml5ValidateTypeExtensionTest extends TestCase
{
    public function testDisableHtml5Validate()
    {
        $formFactoryBuilder = new FormFactoryBuilder();

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder()
            ->getForm();

        $this->assertTrue($form->getConfig()->getOption('required'));

        $form = $formFactoryBuilder->addTypeExtension(new DisableHtml5ValidateTypeExtension())
            ->getFormFactory()
            ->createBuilder()
            ->getForm();

        $this->assertFalse($form->getConfig()->getOption('required'));
    }
}
