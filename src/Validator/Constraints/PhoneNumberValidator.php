<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PhoneNumber) {
            throw new UnexpectedTypeException($constraint, PhoneNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;

        if (!preg_match('/^[1]([3-9])[0-9]{9}$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(PhoneNumber::INVALID_ERROR)
                ->addViolation()
            ;
        }
    }
}
