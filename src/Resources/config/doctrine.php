<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
            ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])

        ->set('siganushka_generic.doctrine.listener.timestampable', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.doctrine.listener.sortable', SortableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])
    ;
};
