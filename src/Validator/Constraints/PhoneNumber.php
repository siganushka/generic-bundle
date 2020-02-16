<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public $lengthMessage = 'This value should have exactly {{ length }} character.';
    public $invalidMessage = 'This value is not valid.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
