<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle;

use Siganushka\GenericBundle\DependencyInjection\Compiler\RemoveByDependencyMappingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveByDependencyMappingPass([
            'siganushka_generic.doctrine.schema_resort_command' => 'doctrine',
            'siganushka_generic.serializer.serializer_dump_command' => 'doctrine',
            'siganushka_generic.serializer.entity_metadata_factory' => 'doctrine',
            'siganushka_generic.twig_extension' => 'twig',
            'siganushka_generic.twig_runtime' => 'twig',
            'siganushka_generic.knp_paginator_decorator' => 'knp_paginator',
        ]));
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
