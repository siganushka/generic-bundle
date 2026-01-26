<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Contracts\Translation\TranslatorInterface;

class MoneyTypeExtension extends AbstractTypeExtension
{
    public const INT32_MAX = 2147483647;

    public function __construct(
        private readonly string $locale = 'en_US',
        private readonly string $currency = 'USD',
        private readonly ?TranslatorInterface $translator = null,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $scale = static fn (Options $options): int => Currencies::getFractionDigits($options['currency']);
        $divisor = static fn (Options $options): int => 10 ** $options['scale'];
        $attr = static fn (Options $options) => ['step' => 1 / $options['divisor']];

        $resolver->setDefaults([
            'currency' => $this->currency,
            'scale' => $scale,
            'divisor' => $divisor,
            'attr' => $attr,
            // @see https://symfony.com/doc/current/reference/forms/types/money.html#input
            'input' => 'integer',
            'html5' => true,
        ]);

        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $formatter = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $options['scale']);
            $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);
            $formatter->setAttribute(\NumberFormatter::GROUPING_USED, 0);

            $messageTemplate = 'This value should be less than or equal to {{ compared_value }}.';
            $messageParameters = ['{{ compared_value }}' => $formatter->format(self::INT32_MAX / $options['divisor'])];

            $message = $this->translator
                ? $this->translator->trans($messageTemplate, $messageParameters, 'validators')
                : strtr($messageTemplate, $messageParameters);

            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new LessThanOrEqual(self::INT32_MAX, message: $message);

            return $constraints;
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            MoneyType::class,
        ];
    }
}
