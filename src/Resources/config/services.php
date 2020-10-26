<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Command\RegionUpdateCommand;
use Siganushka\GenericBundle\Controller\RegionController;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TimestampableSubscriber;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        // Region Controller
        ->set(RegionController::class)
        ->args([
            service(EntityManagerInterface::class),
            service(EventDispatcherInterface::class),
            service(NormalizerInterface::class),
        ])
        ->public()

        // Region Update Command
        ->set(RegionUpdateCommand::class)
        ->args([
            service(EntityManagerInterface::class),
        ])
        ->tag('console.command')

        // Timestampable Subscriber
        ->set(TimestampableSubscriber::class)
        ->tag('doctrine.event_subscriber')

        // Sortable Subscriber
        ->set(SortableSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;
};
