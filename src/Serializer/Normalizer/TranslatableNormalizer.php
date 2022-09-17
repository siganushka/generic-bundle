<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatableNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public const LOCALE = 'locale';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param TranslatableInterface $object
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        return $object->trans($this->translator, $context[self::LOCALE] ?? null);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof TranslatableInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
