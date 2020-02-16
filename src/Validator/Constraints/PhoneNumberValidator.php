<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator
{
    const LENGTH = 11;
    const STARTNUMBER = 1;

    public function validate($value, Constraint $constraint)
    {
        if (self::LENGTH !== mb_strlen($value)) {
            $this->context->buildViolation($constraint->lengthMessage)
                ->setParameter('%length%', self::LENGTH)
                ->addViolation();

            return;
        }

        if (!preg_match('/^[0-9]+$/', $value)) {
            $this->context->buildViolation($constraint->invalidMessage)
                ->setParameter('%value%', $value)
                ->addViolation();
        }

        if (self::STARTNUMBER != mb_substr($value, 0, 1)) {
            $this->context->buildViolation($constraint->invalidMessage)
                ->setParameter('%value%', $value)
                ->addViolation();
        }
    }
}
