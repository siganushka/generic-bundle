<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public const INVALID_ERROR = 'a177cf42-f1a9-4f75-9053-895b67224530';

    public string $message = 'This value is not a valid phone number.';

    /**
     * @var array<string, string>
     */
    protected static $errorNames = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
