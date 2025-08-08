<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Serializer\Normalizer\FormErrorNormalizer;
use Siganushka\GenericBundle\Serializer\Normalizer\KnpPaginationNormalizer;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.serializer.normalizer.form_error', FormErrorNormalizer::class)
            ->arg('$translator', service('translator')->ignoreOnInvalid())
            ->tag('serializer.normalizer')

        ->set('siganushka_generic.serializer.normalizer.knp_pagination', KnpPaginationNormalizer::class)
            ->tag('serializer.normalizer')
    ;
};
