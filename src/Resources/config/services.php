<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\EventListener\FormErrorListener;
use Siganushka\GenericBundle\EventListener\JsonRequestListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Siganushka\GenericBundle\EventListener\ResizeImageListener;

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

        ->set('siganushka_generic.listener.resize_image', ResizeImageListener::class)
            ->tag('kernel.event_subscriber')
    ;
};
