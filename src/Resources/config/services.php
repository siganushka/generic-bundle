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
        // Region Update Command
        ->set(RegionUpdateCommand::class)
        ->arg('$entityManager', service(EntityManagerInterface::class))
        ->tag('console.command')

        // Region Controller
        ->set(RegionController::class)
        ->arg('$entityManager', service(EntityManagerInterface::class))
        ->arg('$dispatcher', service(EventDispatcherInterface::class))
        ->arg('$normalizer', service(NormalizerInterface::class))
        ->tag('controller.service_arguments')

        // Timestampable Subscriber
        ->set(TimestampableSubscriber::class)
        ->tag('doctrine.event_subscriber')

        // Sortable Subscriber
        ->set(SortableSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;
};
