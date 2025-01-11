<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\EventListener\JsonRequestListener;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.listener.json_request', JsonRequestListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.listener.json_response', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')
    ;
};
