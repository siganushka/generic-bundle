<?php

use Siganushka\GenericBundle\Controller\RegionController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('siganushka_generic_region', '/regions')
        ->controller(RegionController::class)
    ;
};
