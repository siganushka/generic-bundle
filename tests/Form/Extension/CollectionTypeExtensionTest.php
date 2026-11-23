<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\CollectionTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\TypeTestCase;

class CollectionTypeExtensionTest extends TypeTestCase
{
    public function testButtons(): void
    {
        $form = $this->factory->create(CollectionType::class, ['a', 'b'], [
            'entry_type' => TextType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);

        $view = $form->createView();

        $addButton = $view->vars['add_button'] ?? null;
        static::assertInstanceOf(FormView::class, $addButton);
        static::assertSame('generic.add', $addButton->vars['label']);
        static::assertContains('collection_add_button', $addButton->vars['block_prefixes']);

        foreach ($view as $entryView) {
            static::assertArrayHasKey('delete_button', $entryView->vars);
        }

        $prototype = $view->vars['prototype'] ?? null;
        static::assertInstanceOf(FormView::class, $prototype);
        static::assertArrayHasKey('delete_button', $prototype->vars);

        $deleteButton = $prototype->vars['delete_button'];
        static::assertInstanceOf(FormView::class, $deleteButton);
        static::assertSame('generic.remove', $deleteButton->vars['label']);
        static::assertContains('collection_delete_button', $deleteButton->vars['block_prefixes']);
    }

    public function testNoButtons(): void
    {
        $form = $this->factory->create(CollectionType::class, [], [
            'entry_type' => TextType::class,
            'allow_add' => false,
            'allow_delete' => false,
        ]);

        $view = $form->createView();

        static::assertArrayNotHasKey('add_button', $view->vars);
        static::assertArrayNotHasKey('delete_button', $view->vars);
        static::assertArrayNotHasKey('prototype', $view->vars);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new CollectionTypeExtension(),
        ];
    }
}
