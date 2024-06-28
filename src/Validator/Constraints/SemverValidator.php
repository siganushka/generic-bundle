<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Validator\Constraints;

use Composer\Semver\VersionParser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SemverValidator extends ConstraintValidator
{
    /**
     * @psalm-param mixed $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Semver) {
            throw new UnexpectedTypeException($constraint, Semver::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;
        if ('' === $value) {
            return;
        }

        try {
            (new VersionParser())->normalize($value);
        } catch (\UnexpectedValueException $th) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Semver::INVALID_ERROR)
                ->addViolation()
            ;
        }
    }
}
