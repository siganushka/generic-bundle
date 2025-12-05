<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CnName extends Constraint
{
    public const INVALID_ERROR = '8189e472-f126-4441-945b-7c42730a916a';

    public string $message = 'This value is not a valid chinese name.';

    protected const ERROR_NAMES = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public function __construct(?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
