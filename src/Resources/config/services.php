<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\EventListener\FormErrorListener;
use Siganushka\GenericBundle\EventListener\JsonRequestListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Siganushka\GenericBundle\EventListener\PublicFileListener;
use Siganushka\GenericBundle\EventListener\ResizeImageListener;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.listener.json_request', JsonRequestListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.json_response', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.form_error', FormErrorListener::class)
            ->arg(0, service('serializer.normalizer.form_error'))
            ->arg(1, service('translator'))
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.public_file', PublicFileListener::class)
            ->arg(0, service('siganushka_generic.utils.public_file'))
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.resize_image', ResizeImageListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.identifier.sequence', SequenceGenerator::class)
            ->alias(SequenceGenerator::class, 'siganushka_generic.identifier.sequence')

        ->set('siganushka_generic.utils.public_file', PublicFileUtils::class)
            ->arg(0, service('url_helper'))
            ->arg(1, param('kernel.project_dir').'/public')
            ->alias(PublicFileUtils::class, 'siganushka_generic.utils.public_file')

        ->set('siganushka_generic.utils.currency', CurrencyUtils::class)
            ->alias(CurrencyUtils::class, 'siganushka_generic.utils.currency')
    ;
};
