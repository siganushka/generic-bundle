<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueField extends Constraint
{
    public $message = 'The value "{{ value }}" is already used.';
    public $em = null;
    public $entityClass = null;
    public $field = null;

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
