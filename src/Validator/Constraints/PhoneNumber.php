<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PhoneNumber extends Constraint
{
    public const string INVALID_ERROR = 'a177cf42-f1a9-4f75-9053-895b67224530';

    public string $message = 'This value is not a valid phone number.';

    protected const array ERROR_NAMES = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
