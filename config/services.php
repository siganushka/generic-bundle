<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\EventListener\JsonResponseListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.json_response_listener', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')
    ;
};
