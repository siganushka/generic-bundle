<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Serializer\Normalizer\GenericSortedNormalizer;
use Siganushka\GenericBundle\Serializer\Normalizer\KnpPaginationNormalizer;
use Siganushka\GenericBundle\Serializer\Normalizer\TranslatableNormalizer;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.serializer.normalizer.generic_sorted', GenericSortedNormalizer::class)
            ->arg(0, service('serializer.normalizer.object'))
            ->tag('serializer.normalizer')

        ->set('siganushka_generic.serializer.normalizer.knp_pagination', KnpPaginationNormalizer::class)
            ->arg(0, service('serializer.normalizer.object'))
            ->tag('serializer.normalizer')

        ->set('siganushka_generic.serializer.normalizer.translatable', TranslatableNormalizer::class)
            ->arg(0, service('translator'))
            ->tag('serializer.normalizer')
    ;
};
