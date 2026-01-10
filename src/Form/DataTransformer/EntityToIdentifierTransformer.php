<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @implements DataTransformerInterface<object, string>
 */
class EntityToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * @param class-string $className
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly string $className,
        private readonly string $identifierField)
    {
    }

    public function transform(mixed $value): mixed
    {
        if (FormUtil::isEmpty($value)) {
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

        if (\is_scalar($result) || $result instanceof \Stringable) {
            return (string) $result;
        }

        throw new TransformationFailedException('Unable to cast value.');
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (FormUtil::isEmpty($value)) {
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

        $repository = $em->getRepository($this->className);
        $result = $repository->findOneBy([$this->identifierField => $value]);

        if (null === $result) {
            throw new TransformationFailedException(\sprintf('An object with identifier key "%s" and value "%s" does not exist!', $this->identifierField, (string) $value));
        }

        return $result;
    }
}
