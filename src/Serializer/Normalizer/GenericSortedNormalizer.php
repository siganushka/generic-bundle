<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\Contracts\Doctrine\ResourceInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GenericSortedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private NormalizerInterface $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param ResourceInterface|mixed $object
     *
     * @return array<string, mixed>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var array<string, mixed> */
        $data = $this->normalizer->normalize($object, $format, $context);

        // move id to first
        if (\array_key_exists('id', $data)) {
            $data = ['id' => $data['id']] + $data;
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof ResourceInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
