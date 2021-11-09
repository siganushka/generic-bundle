<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityToIdentifierTransformer implements DataTransformerInterface
{
    protected $managerRegistry;
    protected $className;
    protected $identifierField;

    public function __construct(ManagerRegistry $managerRegistry, string $className, string $identifierField)
    {
        $this->managerRegistry = $managerRegistry;
        $this->className = $className;
        $this->identifierField = $identifierField;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof $this->className) {
            throw new UnexpectedTypeException($value, $this->className);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        try {
            $result = $propertyAccessor->getValue($value, $this->identifierField);
        } catch (\Throwable $th) {
            throw new TransformationFailedException($th->getMessage(), 0, $th);
        }

        return $result;
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            $em = $this->managerRegistry->getManagerForClass($this->className);
        } catch (\Throwable $th) {
            throw new TransformationFailedException($th->getMessage(), 0, $th);
        }

        $metadata = $em->getClassMetadata($this->className);
        if (!$metadata->hasField($this->identifierField)) {
            throw new TransformationFailedException(sprintf('The field "%s" is not mapped for "%s"!', $this->identifierField, $this->className));
        }

        $repository = $em->getRepository($this->className);
        $result = $repository->findOneBy([$this->identifierField => $value]);

        if (null === $result) {
            throw new TransformationFailedException(sprintf('An object with identifier key "%s" and value "%s" does not exist!', $this->identifierField, $value));
        }

        return $result;
    }
}
