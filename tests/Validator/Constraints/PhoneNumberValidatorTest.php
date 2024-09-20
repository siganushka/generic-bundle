<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<PhoneNumberValidator>
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @dataProvider validPhoneNubmersProvider
     */
    public function testValid(?string $phoneNumber): void
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidPhoneNubmersProvider
     */
    public function testInvalid(string $phoneNumber): void
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', \sprintf('"%s"', $phoneNumber))
            ->setCode(PhoneNumber::INVALID_ERROR)
            ->assertRaised()
        ;
    }

    public static function validPhoneNubmersProvider(): array
    {
        return [
            [null],
            [''],
            ['13000000000'],
            ['14000000000'],
            ['15000000000'],
            ['16000000000'],
            ['17000000000'],
            ['18000000000'],
            ['19000000000'],
        ];
    }

    public static function invalidPhoneNubmersProvider(): array
    {
        return [
            ['1'],
            ['11111111111'],
            ['12222222222'],
            ['1333333333a'],
        ];
    }

    protected function createValidator(): PhoneNumberValidator
    {
        return new PhoneNumberValidator();
    }
}
