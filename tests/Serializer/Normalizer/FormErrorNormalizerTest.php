<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Normalizer;

use Siganushka\GenericBundle\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class FormErrorNormalizerTest extends TypeTestCase
{
    public function testNormalize(): void
    {
        $normalizer = new FormErrorNormalizer();

        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('all')->willReturn([]);

        $formErrorIterator = new FormErrorIterator($form, [
            new FormError('foo'),
            new FormError('bar'),
            new FormError('baz'),
        ]);

        $form->method('getErrors')->willReturn($formErrorIterator);

        static::assertSame([
            'type' => 'https://symfony.com/errors/form',
            'title' => 'Unprocessable Content',
            'status' => 422,
            'detail' => 'foo',
            'errors' => [],
        ], $normalizer->normalize($form));
    }
}
