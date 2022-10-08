<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Normalizer;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Normalizer\TranslatableNormalizer;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Translation\Translator;

class TranslatableNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $enResource = [
            'foo' => 'Hello rambo',
            'bar' => 'Hello rambo: %masterpiece%',
        ];

        $cnResource = [
            'foo' => '你好兰博',
            'bar' => '你好兰博：%masterpiece%',
        ];

        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', $enResource, 'en');
        $translator->addResource('array', $cnResource, 'zh_CN');

        $enNormalizer = new TranslatableNormalizer($translator);
        static::assertSame('Hello rambo', $enNormalizer->normalize(new TranslatableMessage('foo')));
        static::assertSame('Hello rambo: First Blood', $enNormalizer->normalize(new TranslatableMessage('bar', ['%masterpiece%' => 'First Blood'])));

        $cnNormalizer = new TranslatableNormalizer($translator, [TranslatableNormalizer::LOCALE_KEY => 'zh_CN']);
        static::assertSame('你好兰博', $cnNormalizer->normalize(new TranslatableMessage('foo')));
        static::assertSame('你好兰博：第一滴血', $cnNormalizer->normalize(new TranslatableMessage('bar', ['%masterpiece%' => '第一滴血'])));
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new TranslatableNormalizer(new Translator('en'));

        static::assertFalse($normalizer->supportsNormalization(new \stdClass()));
        static::assertTrue($normalizer->supportsNormalization(new TranslatableMessage('foo')));
    }
}
