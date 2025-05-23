<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\ButtonPriorityTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Test\TypeTestCase;

class ButtonPriorityTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $button = $this->factory->create(ButtonType::class);
        $submit = $this->factory->create(SubmitType::class);

        $buttonView = $button->createView();
        $submitView = $submit->createView();
        static::assertSame(-128, $buttonView->vars['priority']);
        static::assertSame(-128, $submitView->vars['priority']);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new ButtonPriorityTypeExtension(),
        ];
    }
}
