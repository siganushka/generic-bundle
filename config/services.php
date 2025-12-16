<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Decorator\DecoratingKnpPaginator;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Siganushka\GenericBundle\Twig\Extension\GenericExtension;
use Siganushka\GenericBundle\Twig\Runtime\GenericExtensionRuntime;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.json_response_listener', JsonResponseListener::class)
            ->tag('kernel.event_subscriber')

        ->set('siganushka_generic.knp_paginator_decorator', DecoratingKnpPaginator::class)
            ->arg('$decorated', service('siganushka_generic.knp_paginator_decorator.inner'))
            ->arg('$requestStack', service('request_stack'))
            ->arg('$pageName', param('knp_paginator.page_name'))
            ->decorate('knp_paginator')

        ->set('siganushka_generic.twig_extension', GenericExtension::class)
            ->tag('twig.extension')

        ->set('siganushka_generic.twig_runtime', GenericExtensionRuntime::class)
            ->tag('twig.runtime')
    ;
};
