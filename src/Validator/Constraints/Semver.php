<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Composer\Semver\VersionParser;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Semver extends Constraint
{
    public const INVALID_ERROR = 'adcb534e-6a3c-4859-b449-440f9dc95c60';

    public string $message = 'This value is not a valid semantic version.';

    protected static $errorNames = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    /**
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (!class_exists(VersionParser::class)) {
            throw new \LogicException(sprintf('The "%s" class requires the "Semver" component. Try running "composer require composer/semver".', self::class));
        }

        parent::__construct($options);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
