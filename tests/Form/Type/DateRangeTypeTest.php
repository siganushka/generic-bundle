<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\DateRangeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class DateRangeTypeTest extends TypeTestCase
{
    public function testBuildForm(): void
    {
        $form = $this->createRangeForm();

        static::assertSame(['startAt', 'endAt'], array_keys($form->createView()->children));
        static::assertFalse($form->get('startAt')->getConfig()->getOption('label'));
        static::assertSame('generic.to', $form->get('endAt')->getConfig()->getOption('label'));

        $constraint = $this->findGreaterThanOrEqual($form);
        static::assertSame('parent.all[startAt].data', $constraint->propertyPath);
        static::assertSame('This value should be greater than or equal to start date.', $constraint->message);
    }

    public function testDateTimeEntryMessage(): void
    {
        $form = $this->createRangeForm(['entry_type' => DateTimeType::class]);

        static::assertSame(
            'This value should be greater than or equal to start datetime.',
            $this->findGreaterThanOrEqual($form)->message
        );
    }

    public function testTimeEntryUsesDefaultMessage(): void
    {
        $form = $this->createRangeForm(['entry_type' => TimeType::class]);

        static::assertSame(
            'This value should be greater than or equal to start datetime.',
            $this->findGreaterThanOrEqual($form)->message
        );
    }

    public function testInvalidMessageOption(): void
    {
        $form = $this->createRangeForm(['invalid_message' => 'Custom message.']);

        static::assertSame('Custom message.', $this->findGreaterThanOrEqual($form)->message);
    }

    public function testExistingConstraintsArePreserved(): void
    {
        $notBlank = new NotBlank();

        $form = $this->createRangeForm(['end_options' => ['constraints' => $notBlank]]);

        $constraints = array_values((array) $form->get('endAt')->getConfig()->getOption('constraints'));

        static::assertCount(2, $constraints);
        static::assertSame($notBlank, $constraints[0]);
        static::assertInstanceOf(GreaterThanOrEqual::class, $constraints[1]);
    }

    public function testExistingConstraintsListArePreserved(): void
    {
        $notBlank = new NotBlank();

        $form = $this->createRangeForm(['end_options' => ['constraints' => [$notBlank]]]);

        $constraints = array_values((array) $form->get('endAt')->getConfig()->getOption('constraints'));

        static::assertCount(2, $constraints);
        static::assertSame($notBlank, $constraints[0]);
        static::assertInstanceOf(GreaterThanOrEqual::class, $constraints[1]);
    }

    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createRangeForm(array $options = []): FormInterface
    {
        $builder = $this->factory->createNamedBuilder('root', FormType::class, null, ['data_class' => null]);
        $builder->add('range', DateRangeType::class, $options);

        return $builder->getForm()->get('range');
    }

    private function findGreaterThanOrEqual(FormInterface $form): GreaterThanOrEqual
    {
        $constraints = array_values((array) $form->get('endAt')->getConfig()->getOption('constraints'));

        $matched = array_values(array_filter(
            $constraints,
            static fn (mixed $constraint) => $constraint instanceof GreaterThanOrEqual
        ));

        static::assertCount(1, $matched);
        static::assertInstanceOf(GreaterThanOrEqual::class, $matched[0]);

        return $matched[0];
    }
}
