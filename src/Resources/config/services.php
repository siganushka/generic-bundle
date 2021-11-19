<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Doctrine\EventListener\SortableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

return static function (ContainerConfigurator $container) {
    $jsonEncodingOptions = param('siganushka_generic.json.encoding_options');

    $container->services()
        ->set('siganushka_generic.listener.json_response', JsonResponseListener::class)
            ->arg(0, $jsonEncodingOptions)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
            ->arg(0, param('siganushka_generic.doctrine.table_prefix'))
            ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])

        ->set('siganushka_generic.doctrine.listener.timestampable', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.doctrine.listener.sortable', SortableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.identifier.generator.sequence', SequenceGenerator::class)
            ->alias(SequenceGenerator::class, 'siganushka_generic.identifier.generator.sequence')
    ;

    if (class_exists(MoneyToLocalizedStringTransformer::class)) {
        $container->services()
            ->set('siganushka_generic.utils.currency', CurrencyUtils::class)
            ->args([
                param('siganushka_generic.currency.scale'),
                param('siganushka_generic.currency.grouping'),
                param('siganushka_generic.currency.rounding_mode'),
                param('siganushka_generic.currency.divisor'),
            ])
            ->alias(CurrencyUtils::class, 'siganushka_generic.utils.currency')
        ;
    }

    if (class_exists(Serializer::class)) {
        $dateTimeNormalizerOptions = [
            DateTimeNormalizer::FORMAT_KEY => param('siganushka_generic.datetime.format'),
            DateTimeNormalizer::TIMEZONE_KEY => param('siganushka_generic.datetime.timezone'),
        ];

        $container->services()
            ->set('siganushka_generic.serializer.encoder.json', JsonEncoder::class)
                ->arg(0, inline_service(JsonEncode::class)->arg(0, [JsonEncode::OPTIONS => $jsonEncodingOptions]))
                ->tag('serializer.encoder', ['priority' => 16])

            ->set('siganushka_generic.serializer.normalizer.datetime', DateTimeNormalizer::class)
                ->arg(0, $dateTimeNormalizerOptions)
                ->tag('serializer.normalizer', ['priority' => 16])
        ;
    }
};
