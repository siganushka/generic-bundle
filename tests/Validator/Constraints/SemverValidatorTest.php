<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\Semver;
use Siganushka\GenericBundle\Validator\Constraints\SemverValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<SemverValidator>
 */
class SemverValidatorTest extends ConstraintValidatorTestCase
{
    /**
     *  @dataProvider validSemversProvider
     */
    public function testValid(?string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidSemversProvider
     */
    public function testInvalid(string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', \sprintf('"%s"', $version))
            ->setCode(Semver::INVALID_ERROR)
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
    }

    public static function invalidSemversProvider(): iterable
    {
        yield ['a'];
        yield ['1.0.0-foo'];
        yield ['1.0.0+foo bar'];
    }

    protected function createValidator(): SemverValidator
    {
        return new SemverValidator();
    }
}
