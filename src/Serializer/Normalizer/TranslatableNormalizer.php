<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatableNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public const LOCALE_KEY = 'translatable_locale';

    private array $defaultContext = [
        self::LOCALE_KEY => null,
    ];

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, array $defaultContext = [])
    {
        $this->translator = $translator;
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param TranslatableInterface|mixed $object
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->trans($this->translator, $context[self::LOCALE_KEY] ?? $this->defaultContext[self::LOCALE_KEY]);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof TranslatableInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
