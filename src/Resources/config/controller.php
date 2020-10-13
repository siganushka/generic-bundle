<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Controller\RegionController;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(RegionController::class)
        ->args([
            service(EntityManagerInterface::class),
        ])
        ->public()
    ;
};
