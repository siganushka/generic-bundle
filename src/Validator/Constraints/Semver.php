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
