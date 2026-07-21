<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Compiler\RemoveByDependencyMappingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveByDependencyMappingPassTest extends TestCase
{
    #[DataProvider('dependencyMappingProvider')]
    public function testAll(array $services, array $dependencyMapping, array $serviceIds): void
    {
        $container = new ContainerBuilder();
        foreach ($services as $key => $value) {
            $container->register($key, $value);
        }

        $compilerPass = new RemoveByDependencyMappingPass($dependencyMapping);
        $compilerPass->process($container);

        static::assertSame($serviceIds, $container->getServiceIds());
    }

    public static function dependencyMappingProvider(): iterable
    {
        yield [
            [],
            [],
            ['service_container'],
        ];
        yield [
            [],
            ['foo' => \stdClass::class],
            ['service_container'],
        ];
        yield [
            ['foo' => \stdClass::class],
            [],
            ['service_container', 'foo'],
        ];
        yield [
            ['foo' => \stdClass::class],
            ['foo' => \stdClass::class],
            ['service_container'],
        ];
        yield [
            ['x' => \stdClass::class, 'y' => \stdClass::class, 'z' => \stdClass::class],
            [],
            ['service_container', 'x', 'y', 'z'],
        ];
        yield [
            ['x' => \stdClass::class, 'y' => \stdClass::class, 'z' => \stdClass::class],
            ['x' => ['y', 'z']],
            ['service_container', 'x', 'y', 'z'],
        ];
        yield [
            ['x' => \stdClass::class, 'y' => \stdClass::class, 'z' => \stdClass::class],
            ['x' => ['y', 'z', 'aaa']],
            ['service_container', 'y', 'z'],
        ];
        yield [
            ['x' => \stdClass::class, 'y' => \stdClass::class, 'z' => \stdClass::class],
            ['z' => 'bbb'],
            ['service_container', 'x', 'y'],
        ];
        yield [
            ['x' => \stdClass::class, 'y' => \stdClass::class, 'z' => \stdClass::class],
            ['z' => ['x', 'y']],
            ['service_container', 'x', 'y', 'z'],
        ];
    }
}
