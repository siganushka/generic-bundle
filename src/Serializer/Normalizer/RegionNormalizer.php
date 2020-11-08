<?php

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Entity\RegionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'code' => $object->getCode(),
            'name' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RegionInterface;
    }
}
