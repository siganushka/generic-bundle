<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Command\DumpSerializationCommand;
use Siganushka\GenericBundle\Serializer\Mapping\EntityMetadataFactory;
use Siganushka\GenericBundle\Serializer\Normalizer\FormErrorNormalizer;
use Siganushka\GenericBundle\Serializer\Normalizer\KnpPaginationNormalizer;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.serializer.entity_metadata_factory', EntityMetadataFactory::class)
            ->arg('$decorated', service('siganushka_generic.serializer.entity_metadata_factory.inner'))
            ->arg('$registry', service('doctrine'))
            ->decorate('serializer.mapping.class_metadata_factory')

        ->set('siganushka_generic.serializer.dump_serialization_command', DumpSerializationCommand::class)
            ->arg('$managerRegistry', service('doctrine'))
            ->arg('$metadataFactory', service('serializer.mapping.class_metadata_factory'))
            ->arg('$serializationDir', '%kernel.project_dir%/config/serializer')
            ->tag('console.command')

        ->set('siganushka_generic.serializer.form_error_normalizer', FormErrorNormalizer::class)
            ->arg('$translator', service('translator')->ignoreOnInvalid())
            ->tag('serializer.normalizer')

        ->set('siganushka_generic.serializer.knp_pagination_normalizer', KnpPaginationNormalizer::class)
            ->tag('serializer.normalizer')
    ;
};
