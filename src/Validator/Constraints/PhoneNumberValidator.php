<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PhoneNumber) {
            throw new UnexpectedTypeException($constraint, PhoneNumber::class);
        }

        if (null === $value) {
            return;
        }

        $value = (string) $value;
        if (!preg_match('/^[1]([3-9])[0-9]{9}$/', $value)) {
            $this->context->buildViolation($constraint->invalidMessage)
                ->setParameter('%value%', $value)
                ->addViolation();
        }
    }
}
