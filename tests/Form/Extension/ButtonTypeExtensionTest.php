<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\ButtonTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Test\TypeTestCase;

class ButtonTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $button = $this->factory->create(ButtonType::class);
        $submit = $this->factory->create(SubmitType::class);

        $buttonView = $button->createView();
        $submitView = $submit->createView();
        static::assertSame(-16, $buttonView->vars['priority']);
        static::assertSame(-16, $submitView->vars['priority']);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new ButtonTypeExtension(),
        ];
    }
}
