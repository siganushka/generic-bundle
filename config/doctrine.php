<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\ToolEvents;
use Siganushka\GenericBundle\Command\SchemaResortCommand;
use Siganushka\GenericBundle\Doctrine\EventListener\DeletableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\MappingOverrideListener;
use Siganushka\GenericBundle\Doctrine\EventListener\NestableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\SchemaResortListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.doctrine.mapping_override_listener', MappingOverrideListener::class)
            ->arg('$mappingOverride', param('siganushka_generic.doctrine.mapping_override'))
            ->tag('doctrine.event_listener', ['event' => Events::loadClassMetadata])

        ->set('siganushka_generic.doctrine.table_prefix_listener', TablePrefixListener::class)
            ->arg('$prefix', param('siganushka_generic.doctrine.table_prefix'))
            ->tag('doctrine.event_listener', ['event' => Events::loadClassMetadata])

        ->set('siganushka_generic.doctrine.nestable_listener', NestableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::loadClassMetadata])

        ->set('siganushka_generic.doctrine.timestampable_listener', TimestampableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::prePersist])
            ->tag('doctrine.event_listener', ['event' => Events::preUpdate])

        ->set('siganushka_generic.doctrine.deletable_listener', DeletableListener::class)
            ->tag('doctrine.event_listener', ['event' => Events::onFlush])

        ->set('siganushka_generic.doctrine.schema_resort_listener', SchemaResortListener::class)
            ->tag('doctrine.event_listener', ['event' => ToolEvents::postGenerateSchemaTable])

        ->set('siganushka_generic.doctrine.schema_resort_command', SchemaResortCommand::class)
            ->arg('$managerRegistry', service('doctrine')->nullOnInvalid())
            ->tag('console.command')
    ;
};
