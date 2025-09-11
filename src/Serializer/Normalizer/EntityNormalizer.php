<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EntityNormalizer implements NormalizerInterface
{
    public const ASSOCIATION_ATTRIBUTES = 'association_attributes';

    /**
     * @var array<string, bool>
     */
    private array $supportedTypes;

    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly RequestStack $requestStack,
        private readonly ManagerRegistry $managerRegistry)
    {
    }

    /**
     * @param object $object
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $ref = new \ReflectionMethod($this->normalizer, 'extractAttributes');
        /** @var array<string> */
        $extractAttributes = $ref->invoke($this->normalizer, $object, $format, $context);

        /** @var ClassMetadata<object> */
        $metadata = $this->managerRegistry->getManagerForClass($object::class)?->getClassMetadata($object::class);
        foreach ($metadata->getAssociationMappings() as $name => $mapping) {
            $ref = new \ReflectionClass($mapping->targetEntity);
            if (false === $ref->implementsInterface(\Stringable::class) && false !== $key = array_search($name, $extractAttributes)) {
                unset($extractAttributes[$key]);
            }
        }

        $associationAttributes = array_replace_recursive(
            (array) ($context[self::ASSOCIATION_ATTRIBUTES] ?? []),
            $this->requestStack->getCurrentRequest()?->query->all('_associations') ?? [],
        );

        /** @var array */
        $data = $this->normalizer->normalize($object, $format, [
            AbstractNormalizer::ATTRIBUTES => array_merge($extractAttributes, $associationAttributes),
        ]);

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return \is_object($data) && $this->managerRegistry->getManagerForClass($data::class) && !\array_key_exists(AbstractNormalizer::ATTRIBUTES, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        if (isset($this->supportedTypes)) {
            return $this->supportedTypes;
        }

        $supportedTypes = [];
        foreach ($this->managerRegistry->getManagers() as $em) {
            foreach ($em->getMetadataFactory()->getAllMetadata() as $classMetadata) {
                $supportedTypes[$classMetadata->getName()] = true;
            }
        }

        return $this->supportedTypes = $supportedTypes;
    }
}
