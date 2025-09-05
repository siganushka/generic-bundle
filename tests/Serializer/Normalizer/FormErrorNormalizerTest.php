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

        static::assertSame([
            'type' => 'test type',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'foo',
            'errors' => [],
        ], $normalizer->normalize($form, context: [
            FormErrorNormalizer::TYPE => 'test type',
            FormErrorNormalizer::STATUS => 400,
        ]));

        $normalizer = new FormErrorNormalizer([
            FormErrorNormalizer::TYPE => 'test type aaa',
            FormErrorNormalizer::STATUS => 400,
        ]);

        static::assertSame([
            'type' => 'test type aaa',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'foo',
            'errors' => [],
        ], $normalizer->normalize($form));
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new FormErrorNormalizer();
        static::assertFalse($normalizer->supportsNormalization(new \stdClass()));

        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(false);
        $form->method('isValid')->willReturn(false);
        static::assertFalse($normalizer->supportsNormalization($form));

        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);
        static::assertTrue($normalizer->supportsNormalization($form));
    }

    public function testGetSupportedTypes(): void
    {
        $normalizer = new FormErrorNormalizer();
        static::assertSame([
            FormInterface::class => false,
        ], $normalizer->getSupportedTypes(null));
    }
}
