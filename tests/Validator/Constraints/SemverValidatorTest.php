<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\Semver;
use Siganushka\GenericBundle\Validator\Constraints\SemverValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<SemverValidator>
 * @psalm-suppress PropertyNotSetInConstructor
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

    public static function validSemversProvider(): array
    {
        return [
            [null],
            [''],
            ['0.1.0'],
            ['1.0.0-dev'],
            ['1.2.3.4'],
            ['1'],
        ];
    }

    public static function invalidSemversProvider(): array
    {
        return [
            ['a'],
            ['1.0.0-foo'],
            ['1.0.0+foo bar'],
        ];
    }

    protected function createValidator(): SemverValidator
    {
        return new SemverValidator();
    }
}
