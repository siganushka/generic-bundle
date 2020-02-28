<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public $message = 'This value is not a valid phone number.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
