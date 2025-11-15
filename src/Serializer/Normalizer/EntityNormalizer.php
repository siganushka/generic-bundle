<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Serializer\Mapping\EntityClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EntityNormalizer implements NormalizerInterface
{
    public const ITEM = 'siganushka_generic_serialization_item';
    public const COLLECTION = 'siganushka_generic_serialization_collection';

    public function __construct(private readonly NormalizerInterface $normalizer)
    {
    }

    /**
     * @param object $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (\array_key_exists(self::ITEM, $context)) {
            $context[AbstractNormalizer::GROUPS][] = EntityClassMetadataFactory::getGroupForItem($object::class);
        } elseif (\array_key_exists(self::COLLECTION, $context)) {
            $context[AbstractNormalizer::GROUPS][] = EntityClassMetadataFactory::getGroupForCollection($object::class);
        }

        // remove entity normalizer mark.
        unset($context[self::ITEM], $context[self::COLLECTION]);

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return \array_key_exists(self::ITEM, $context) || \array_key_exists(self::COLLECTION, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => true,
        ];
    }
}
