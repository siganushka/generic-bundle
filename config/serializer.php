<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Serializer\Mapping\EntityClassMetadataFactory;
use Siganushka\GenericBundle\Serializer\Normalizer\FormErrorNormalizer;
use Siganushka\GenericBundle\Serializer\Normalizer\KnpPaginationNormalizer;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.serializer.entity_mapping', EntityClassMetadataFactory::class)
            ->arg('$decorated', service('siganushka_generic.serializer.entity_mapping.inner'))
            ->arg('$managerRegistry', service('doctrine'))
            ->decorate('serializer.mapping.class_metadata_factory')

        ->set('siganushka_generic.serializer.form_error_normalizer', FormErrorNormalizer::class)
            ->arg('$translator', service('translator')->ignoreOnInvalid())
            ->tag('serializer.normalizer')

        ->set('siganushka_generic.serializer.knp_pagination_normalizer', KnpPaginationNormalizer::class)
            ->tag('serializer.normalizer')
    ;
};
