<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\Html5ValidationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

class Html5ValidationTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $form = $this->factory->create(TextType::class);

        $view = $form->createView();
        static::assertFalse($view->vars['required']);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new Html5ValidationTypeExtension(),
        ];
    }
}
