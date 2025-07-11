<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Doctrine\EventListener\DeletableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\MappingOverrideListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.doctrine.listener.mapping_override', MappingOverrideListener::class)
            ->arg(0, param('siganushka_generic.doctrine.mapping_override'))
            ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])

        ->set('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
            ->arg(0, param('siganushka_generic.doctrine.table_prefix'))
            ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])

        ->set('siganushka_generic.doctrine.listener.timestampable', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'prePersist'])
            ->tag('doctrine.event_listener', ['event' => 'preUpdate'])

        ->set('siganushka_generic.doctrine.listener.deletable', DeletableListener::class)
            ->tag('doctrine.event_listener', ['event' => 'onFlush'])
    ;
};
