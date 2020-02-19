<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[1]([3-9])[0-9]{9}$/', $value)) {
            $this->context->buildViolation($constraint->invalidMessage)
                ->setParameter('%value%', $value)
                ->addViolation();
        }
    }
}
