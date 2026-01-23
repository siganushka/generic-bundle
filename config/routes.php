<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    if ('dev' === $routes->env()) {
        $routes->add('_siganushka_generic_form', '/_form')
            ->controller('siganushka_generic.form.controller')
        ;
    }
};
