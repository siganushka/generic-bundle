<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Doctrine\EventListener\SortableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

return static function (ContainerConfigurator $container) {
    $jsonEncodeOptions = param('siganushka_generic.json_encode_options');

    $container->services()
        ->set('siganushka_generic.listener.json_response', JsonResponseListener::class)
            ->arg(0, $jsonEncodeOptions)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
            ->arg(0, param('siganushka_generic.table_prefix'))
            ->tag('doctrine.event_subscriber')

        ->set('siganushka_generic.doctrine.listener.timestampable', TimestampableListener::class)
            ->tag('doctrine.event_subscriber')

        ->set('siganushka_generic.doctrine.listener.sortable', SortableListener::class)
            ->tag('doctrine.event_subscriber')
    ;

    if (class_exists(Serializer::class)) {
        $dateTimeNormalizerOptions = [
            DateTimeNormalizer::FORMAT_KEY => param('siganushka_generic.datetime_format'),
            DateTimeNormalizer::TIMEZONE_KEY => param('siganushka_generic.datetime_timezone'),
        ];

        $container->services()
            ->set('siganushka_generic.serializer.encoder.json', JsonEncoder::class)
                ->arg(0, inline_service(JsonEncode::class)->arg(0, [JsonEncode::OPTIONS => $jsonEncodeOptions]))
                ->tag('serializer.encoder', ['priority' => 16])

            ->set('siganushka_generic.serializer.normalizer.datetime', DateTimeNormalizer::class)
                ->arg(0, $dateTimeNormalizerOptions)
                ->tag('serializer.normalizer', ['priority' => 16])
            ;
    }
};
