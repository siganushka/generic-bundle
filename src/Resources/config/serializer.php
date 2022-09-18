<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Serializer\Normalizer\TranslatableNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('siganushka_generic.serializer.encoder.json', JsonEncoder::class)
            ->tag('serializer.encoder', ['priority' => 16])

        ->set('siganushka_generic.serializer.normalizer.translatable', TranslatableNormalizer::class)
            ->arg(0, service('translator'))
            ->tag('serializer.normalizer')
    ;
};
