<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Extension;

use Siganushka\GenericBundle\Form\Extension\MoneyTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MoneyTypeExtensionTest extends TypeTestCase
{
    public function testAll(): void
    {
        $money = $this->factory->create(MoneyType::class);

        $moneyConfig = $money->getConfig();
        static::assertSame(2, $moneyConfig->getOption('scale'));
        static::assertSame(100, $moneyConfig->getOption('divisor'));
        static::assertSame('USD', $moneyConfig->getOption('currency'));
        static::assertSame('integer', $moneyConfig->getOption('input'));

        /** @var array<int, Constraint> */
        $constraints = $moneyConfig->getOption('constraints');
        static::assertInstanceOf(LessThanOrEqual::class, $constraints[0]);
    }

    protected function getTypeExtensions(): array
    {
        $validator = $this->createMock(ValidatorInterface::class);

        return [
            new FormTypeValidatorExtension($validator),
            new MoneyTypeExtension(),
        ];
    }
}
