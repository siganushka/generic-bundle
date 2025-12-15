<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Decorator\DecoratingKnpPaginator;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.json_response_listener', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.knp_paginator_decorator', DecoratingKnpPaginator::class)
            ->arg('$decorated', service('siganushka_generic.knp_paginator_decorator.inner'))
            ->arg('$requestStack', service('request_stack'))
            ->arg('$pageName', param('knp_paginator.page_name'))
            ->decorate('knp_paginator')
    ;
};
