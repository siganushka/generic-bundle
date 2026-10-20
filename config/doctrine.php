<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\Events;
use Siganushka\GenericBundle\Doctrine\EventListener\DeletableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\NestableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.doctrine.table_prefix_listener', TablePrefixListener::class)
            ->arg('$prefix', param('siganushka_generic.doctrine.table_prefix'))
            ->tag('doctrine.event_listener', ['event' => Events::loadClassMetadata])

        ->set('siganushka_generic.doctrine.nestable_listener', NestableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::loadClassMetadata])

        ->set('siganushka_generic.doctrine.timestampable_listener', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::prePersist])
            ->tag('doctrine.event_listener', ['event' => Events::preUpdate])

        ->set('siganushka_generic.doctrine.deletable_listener', DeletableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::onFlush, 'priority' => 128])
    ;
};
