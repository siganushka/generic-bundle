<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Compiler\DependencyCheckPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DependencyCheckPassTest extends TestCase
{
    #[DataProvider('dependencyMappingProvider')]
    public function testAll(array $dependencyServiceIds, array $expectedServiceIds): void
    {
        $serviceIds = array_merge($dependencyServiceIds, array_keys(DependencyCheckPass::DEPENDENCY_MAPPING));

        $container = new ContainerBuilder();
        array_walk($serviceIds, static fn (string $id) => $container->register($id, \stdClass::class));

        $compilerPass = new DependencyCheckPass();
        $compilerPass->process($container);

        static::assertSame($expectedServiceIds, $container->getServiceIds());
    }

    public static function dependencyMappingProvider(): iterable
    {
        yield [
            [],
            ['service_container'],
        ];
        yield [
            ['doctrine'],
            ['service_container', 'doctrine', 'siganushka_generic.doctrine.schema_resort_command', 'siganushka_generic.serializer.serializer_dump_command', 'siganushka_generic.serializer.entity_metadata_factory'],
        ];
        yield [
            ['twig'],
            ['service_container', 'twig', 'siganushka_generic.twig_extension', 'siganushka_generic.twig_runtime'],
        ];
        yield [
            ['knp_paginator'],
            ['service_container', 'knp_paginator', 'siganushka_generic.knp_paginator_decorator'],
        ];
    }
}
