<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Siganushka\GenericBundle\EventListener\PublicFileDataListener;
use Siganushka\GenericBundle\EventListener\ResizeImageListener;
use Siganushka\GenericBundle\Form\Extension\DisableHtml5Validation;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\Form\Form;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('siganushka_generic.listener.json_response', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.public_file_data', PublicFileDataListener::class)
            ->arg(0, service('siganushka_generic.utils.public_file'))
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.resize_image', ResizeImageListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
            ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])

        ->set('siganushka_generic.doctrine.listener.timestampable', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.doctrine.listener.sortable', SortableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.identifier.sequence', SequenceGenerator::class)
            ->alias(SequenceGenerator::class, 'siganushka_generic.identifier.sequence')

        ->set('siganushka_generic.utils.public_file', PublicFileUtils::class)
            ->arg(0, service('url_helper'))
            ->arg(1, param('kernel.project_dir').'/public')
            ->alias(PublicFileUtils::class, 'siganushka_generic.utils.public_file')

        ->set('siganushka_generic.utils.currency', CurrencyUtils::class)
            ->alias(CurrencyUtils::class, 'siganushka_generic.utils.currency')
    ;

    if (class_exists(Form::class)) {
        $container->services()
            ->set('siganushka_generic.form.type_extension.disable_html5_validation', DisableHtml5Validation::class)
                ->tag('form.type_extension')
        ;
    }

    if (class_exists(Serializer::class)) {
        $container->services()
            ->set('siganushka_generic.serializer.encoder.json', JsonEncoder::class)
                ->tag('serializer.encoder', ['priority' => 16])
        ;
    }
};
