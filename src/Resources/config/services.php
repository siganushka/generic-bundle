<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Command\RegionsUpdateCommand;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TimestampableSubscriber;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set(RegionsUpdateCommand::class)
        ->args([
            service(HttpClientInterface::class),
            service(EntityManagerInterface::class),
        ])
        ->tag('console.command')
    ;

    $services
        ->set(TimestampableSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;

    $services
        ->set(SortableSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;
};
