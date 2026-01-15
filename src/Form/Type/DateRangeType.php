<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class DateRangeType extends AbstractType
{
    /**
     * @param array{
     *  entry_type: class-string<FormTypeInterface>,
     *  start_options: array,
     *  end_options: array
     * } $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', $options['entry_type'], $options['start_options'])
            ->add('endAt', $options['entry_type'], $options['end_options'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $invalidMessage = function (Options $options): string {
            return match ($options['entry_type']) {
                DateType::class => 'This value should be greater than or equal to start date.',
                default => 'This value should be greater than or equal to start datetime.',
            };
        };

        $resolver->setDefaults([
            'entry_type' => DateType::class,
            'start_options' => [],
            'end_options' => [],
            'invalid_message' => $invalidMessage,
            'inherit_data' => true,
        ]);

        $resolver->setAllowedValues('entry_type', [DateTimeType::class, DateType::class, TimeType::class]);
        $resolver->setAllowedTypes('start_options', ['array']);
        $resolver->setAllowedTypes('end_options', ['array']);

        $resolver->setNormalizer('start_options', function (Options $options, array $value) {
            $value['label'] ??= false;

            return $value;
        });

        $resolver->setNormalizer('end_options', function (Options $options, array $value) {
            $gte = new GreaterThanOrEqual(propertyPath: 'parent.all[startAt].data', message: $options['invalid_message']);

            $constraints = $value['constraints'] ?? [];
            $newConstraints = \is_array($constraints) ? $constraints + [$gte] : [$constraints, $gte];

            $value['constraints'] = $newConstraints;
            $value['label'] ??= 'generic.to';

            return $value;
        });
    }
}
