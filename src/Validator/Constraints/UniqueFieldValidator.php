<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueFieldValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueField) {
            throw new UnexpectedTypeException($constraint, UniqueField::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($constraint->field)) {
            throw new UnexpectedTypeException($constraint->field, 'string');
        }

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $em = $this->registry->getManagerForClass($constraint->entityClass);

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', $constraint->entityClass));
            }
        }

        $class = $em->getClassMetadata($constraint->entityClass);
        if (!$class->hasField($constraint->field) && !$class->hasAssociation($constraint->field)) {
            throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->field));
        }

        $repository = $em->getRepository($constraint->entityClass);
        $result = $repository->findOneBy([$constraint->field => $value]);

        if (null === $result) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->setInvalidValue($value)
            ->setCause($result)
            ->addViolation();
    }
}
