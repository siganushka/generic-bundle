<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public $invalidMessage = 'This phone number is not valid.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
