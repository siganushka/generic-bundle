<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\CsrfTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CsrfTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $form = $this->factory->create(FormType::class);
        $text = $this->factory->create(TextType::class);

        static::assertFalse($form->getConfig()->getOption('csrf_protection'));
        static::assertTrue($text->getConfig()->getOption('allow_extra_fields'));
    }

    protected function getTypeExtensions(): array
    {
        $request = new Request(attributes: ['_stateless' => true]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return [
            new CsrfTypeExtension($requestStack),
        ];
    }
}
