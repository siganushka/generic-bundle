<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    const INVALID_ERROR = 'a177cf42-f1a9-4f75-9053-895b67224530';

    protected static $errorNames = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public $message = 'This value is not a valid phone number.';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
