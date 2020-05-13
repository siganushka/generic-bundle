<?php

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new PhoneNumberValidator();
    }

    /**
     *  @dataProvider getValidPhoneNubmers
     */
    public function testValid(?string $phoneNumber)
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidPhoneNubmers
     */
    public function testInvalid(string $phoneNumber)
    {
        $constraint = new PhoneNumber();

        $this->validator->validate($phoneNumber, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', sprintf('"%s"', $phoneNumber))
            ->setCode(PhoneNumber::INVALID_ERROR)
            ->assertRaised();
    }

    public function getValidPhoneNubmers()
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

    public function getInvalidPhoneNubmers()
    {
        return [
            ['1'],
            ['11111111111'],
            ['12222222222'],
            ['1333333333a'],
        ];
    }
}