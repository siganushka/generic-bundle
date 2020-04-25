<?php

namespace Siganushka\GenericBundle\Validator\Constraints;

use Composer\Semver\VersionParser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\LogicException;

/**
 * @Annotation
 */
class Semver extends Constraint
{
    const INVALID_ERROR = 'adcb534e-6a3c-4859-b449-440f9dc95c60';

    protected static $errorNames = [
        self::INVALID_ERROR => 'INVALID_ERROR',
    ];

    public $message = 'This value is not a valid semantic version.';

    public function __construct($options = null)
    {
        if (!class_exists(VersionParser::class)) {
            throw new LogicException('The semantic version is required to use the Semver constraint. Try running "composer require composer/semver".');
        }

        parent::__construct($options);
    }

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
