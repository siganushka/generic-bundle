<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\ChoiceTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Test\TypeTestCase;

class ChoiceTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $choice = $this->factory->create(ChoiceType::class);

        $choiceConfig = $choice->getConfig();
        $choiceView = $choice->createView();
        static::assertSame('generic.choice', $choiceConfig->getOption('placeholder'));
        static::assertSame('generic.choice', $choiceView->vars['placeholder']);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new ChoiceTypeExtension(),
        ];
    }
}
