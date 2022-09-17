<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Composer\Semver\VersionParser;
use Siganushka\GenericBundle\Validator\Constraints\Semver;
use Siganushka\GenericBundle\Validator\Constraints\SemverValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class SemverValidatorTest extends ConstraintValidatorTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(VersionParser::class)) {
            static::markTestSkipped('Skip tests.');
        }

        parent::setUp();
    }

    /**
     *  @dataProvider getValidSemvers
     */
    public function testValid(?string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidSemvers
     */
    public function testInvalid(string $version): void
    {
        $constraint = new Semver();

        $this->validator->validate($version, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', sprintf('"%s"', $version))
            ->setCode(Semver::INVALID_ERROR)
            ->assertRaised()
        ;
    }

    public function getValidSemvers(): array
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

    public function getInvalidSemvers(): array
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
