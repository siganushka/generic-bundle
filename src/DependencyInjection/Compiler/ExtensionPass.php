<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionPass implements CompilerPassInterface
{
    public const DEPENDENCY_MAPPING = [
        'siganushka_generic.doctrine.schema_resort_command' => 'doctrine',
        'siganushka_generic.serializer.dump_serialization_command' => 'doctrine',
        'siganushka_generic.serializer.entity_metadata_factory' => 'doctrine',
        'siganushka_generic.twig_extension' => 'twig',
        'siganushka_generic.twig_runtime' => 'twig',
        'siganushka_generic.knp_paginator_decorator' => 'knp_paginator',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::DEPENDENCY_MAPPING as $serviceId => $dependencyId) {
            if (!$container->hasDefinition($dependencyId)) {
                $container->removeDefinition($serviceId);
            }
        }
    }
}
