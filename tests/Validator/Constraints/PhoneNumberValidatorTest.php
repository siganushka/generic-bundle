<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<PhoneNumberValidator>
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

    public static function validPhoneNubmersProvider(): iterable
    {
        yield [null];
        yield [''];
        yield ['13000000000'];
        yield ['14000000000'];
        yield ['15000000000'];
        yield ['16000000000'];
        yield ['17000000000'];
        yield ['18000000000'];
        yield ['19000000000'];
    }

    public static function invalidPhoneNubmersProvider(): iterable
    {
        yield ['1'];
        yield ['11111111111'];
        yield ['12222222222'];
        yield ['1333333333a'];
    }

    protected function createValidator(): PhoneNumberValidator
    {
        return new PhoneNumberValidator();
    }
}
