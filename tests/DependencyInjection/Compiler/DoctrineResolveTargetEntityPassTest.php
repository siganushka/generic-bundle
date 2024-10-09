<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineResolveTargetEntityPassTest extends TestCase
{
    public function testDefault(): void
    {
        $container = $this->createContainerWithMappingOverride([]);

        $pass = new DoctrineResolveTargetEntityPass();
        $pass->process($container);

        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        static::assertSame([], $definition->getMethodCalls());
        static::assertSame([], $definition->getTag('doctrine.event_listener'));
    }

    public function testMappingOverride(): void
    {
        $container = $this->createContainerWithMappingOverride([Foo::class => Bar::class]);

        $pass = new DoctrineResolveTargetEntityPass();
        $pass->process($container);

        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        static::assertSame([
            [
                'addResolveTargetEntity',
                [Foo::class, Bar::class, []],
            ],
        ], $definition->getMethodCalls());

        static::assertSame([
            ['event' => 'loadClassMetadata'],
            ['event' => 'onClassMetadataNotFound'],
        ], $definition->getTag('doctrine.event_listener'));
    }

    private function createContainerWithMappingOverride(array $mappingOverride): ContainerBuilder
    {
        $definition = new Definition('%doctrine.orm.listeners.resolve_target_entity.class%');

        $container = new ContainerBuilder();
        $container->setParameter('siganushka_generic.doctrine.mapping_override', $mappingOverride);
        $container->setDefinition('doctrine.orm.listeners.resolve_target_entity', $definition);

        return $container;
    }
}
