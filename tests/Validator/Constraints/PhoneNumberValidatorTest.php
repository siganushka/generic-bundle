<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\Attributes\DataProvider;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<PhoneNumberValidator>
 */
class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    #[DataProvider('validPhoneNubmersProvider')]
    public function testValid(?string $phoneNumber): void
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);
        $this->assertNoViolation();
    }

    #[DataProvider('invalidPhoneNubmersProvider')]
    public function testInvalid(string $phoneNumber): void
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', \sprintf('"%s"', $phoneNumber))
            ->setCode($constraint::INVALID_ERROR)
            ->assertRaised()
        ;
    }

    public static function validPhoneNubmersProvider(): iterable
    {
        yield [null];
        yield [''];
        yield ['13912345678'];
        yield ['18688886666'];
        yield ['18900001111'];
        yield ['17012345678'];
        yield ['13010012002'];
        yield ['15899887766'];
        yield ['17301010101'];
        yield ['19955554444'];
        yield ['16611223344'];
        yield ['19877770000'];
    }

    public static function invalidPhoneNubmersProvider(): iterable
    {
        yield ['1391234567'];
        yield ['139123456789'];
        yield ['11012345678'];
        yield ['12312345678'];
        yield ['10000000000'];
        yield ['1391234567X'];
        yield ['139 12345678'];
        yield ['abcdeffg'];
        yield ['1391234567A'];
    }

    protected function createValidator(): PhoneNumberValidator
    {
        return new PhoneNumberValidator();
    }
}
