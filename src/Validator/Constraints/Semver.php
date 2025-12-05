<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Composer\Semver\VersionParser;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Semver extends Constraint
{
    public const INVALID_ERROR = 'adcb534e-6a3c-4859-b449-440f9dc95c60';

    public string $message = 'This value is not a valid semantic version.';

    protected const ERROR_NAMES = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public function __construct(?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;

        if (!class_exists(VersionParser::class)) {
            throw new \LogicException(\sprintf('The "%s" class requires the "Semver" component. Try running "composer require composer/semver".', self::class));
        }
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
