<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Validator\Constraints;

use Siganushka\GenericBundle\Validator\Constraints\CnName;
use Siganushka\GenericBundle\Validator\Constraints\CnNameValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<CnNameValidator>
 */
class CnNameValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @dataProvider validCnNameProvider
     */
    public function testValid(?string $cnName): void
    {
        $constraint = new CnName();

        $this->validator->validate($cnName, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider invalidCnNameProvider
     */
    public function testInvalid(string $cnName): void
    {
        $constraint = new CnName();

        $this->validator->validate($cnName, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', \sprintf('"%s"', $cnName))
            ->setCode($constraint::INVALID_ERROR)
            ->assertRaised()
        ;
    }

    public static function validCnNameProvider(): iterable
    {
        yield [null];
        yield [''];
        yield ['张三丰'];
        yield ['李白'];
        yield ['王美丽'];
        yield ['欧阳修'];
        yield ['赵钱孙李'];
        yield ['孔乙己'];
        yield ['林月如'];
        yield ['诸葛亮'];
        yield ['东方不败'];
        yield ['慕容复'];
    }

    public static function invalidCnNameProvider(): iterable
    {
        yield ['王'];
        yield ['一'];
        yield ['张1三'];
        yield ['李2'];
        yield ['王 小明'];
        yield ['赵 钱'];
        yield ['林.月如'];
        yield ['李-白'];
        yield ['诸葛亮孔明'];
        yield ['上官欧阳李'];
        yield ['JohnDoe'];
        yield ['Alice'];
    }

    protected function createValidator(): CnNameValidator
    {
        return new CnNameValidator();
    }
}
