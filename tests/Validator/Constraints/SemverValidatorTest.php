<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\Attributes\DataProvider;
use Siganushka\GenericBundle\Validator\Constraints\Semver;
use Siganushka\GenericBundle\Validator\Constraints\SemverValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<SemverValidator>
 */
class SemverValidatorTest extends ConstraintValidatorTestCase
{
    #[DataProvider('validSemversProvider')]
    public function testValid(?string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);
        $this->assertNoViolation();
    }

    #[DataProvider('invalidSemversProvider')]
    public function testInvalid(string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', \sprintf('"%s"', $version))
            ->setCode($constraint::INVALID_ERROR)
            ->assertRaised()
        ;
    }

    public static function validSemversProvider(): iterable
    {
        yield [null];
        yield [''];
        yield ['0.1.0'];
        yield ['1.0.0-dev'];
        yield ['1.2.3.4'];
        yield ['1'];
        yield ['01.0.0'];
        yield ['1.0'];
        yield ['1.0.0.0'];
        yield ['1.0.0'];
        yield ['1.0.0-'];
        yield ['1.0.0-alpha.1'];
        yield ['10.5.2+build.1'];
    }

    public static function invalidSemversProvider(): iterable
    {
        yield ['a'];
        yield ['1.0.0-foo'];
        yield ['1.0.0+foo bar'];
        yield ['1.0.0-dev.20251205'];
        yield ['1.0.0-dev-20251205'];
        yield ['a.b.c'];
        yield ['1.0.0-!tag'];
        yield ['1.0.0-..tag'];
    }

    protected function createValidator(): SemverValidator
    {
        return new SemverValidator();
    }
}
