<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @implements DataTransformerInterface<object, mixed>
 */
class EntityToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * @psalm-param class-string $className
     */
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private string $className,
        private string $identifierField)
    {
    }

    public function transform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof $this->className) {
            throw new TransformationFailedException('Invalid class name.');
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        try {
            $result = $propertyAccessor->getValue($value, $this->identifierField);
        } catch (\Throwable $th) {
            throw new TransformationFailedException($th->getMessage(), 0, $th);
        }

        return (string) $result;
    }

    public function reverseTransform(mixed $value): ?object
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!\is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        try {
            $em = $this->managerRegistry->getManagerForClass($this->className);
        } catch (\Throwable $th) {
            throw new TransformationFailedException($th->getMessage(), 0, $th);
        }

        if (null === $em) {
            throw new TransformationFailedException('Unable to get manager.');
        }

        $metadata = $em->getClassMetadata($this->className);
        if (!$metadata->hasField($this->identifierField)) {
            throw new TransformationFailedException(sprintf('The field "%s" is not mapped for "%s"!', $this->identifierField, $this->className));
        }

        $repository = $em->getRepository($this->className);
        $result = $repository->findOneBy([$this->identifierField => $value]);

        if (null === $result) {
            throw new TransformationFailedException(sprintf('An object with identifier key "%s" and value "%s" does not exist!', $this->identifierField, (string) $value));
        }

        return $result;
    }
}
