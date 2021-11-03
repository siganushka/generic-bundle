<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 * @coversNothing
 */
final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_generic');
        $container->compile();

        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));

        if (class_exists(Serializer::class)) {
            static::assertTrue($container->hasDefinition('siganushka_generic.serializer.encoder.json'));
            static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.datetime'));
        }
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'table_prefix' => 'test_',
        ];

        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_generic', $configs);
        $container->compile();

        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
    }

    protected function createContainer()
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new SiganushkaGenericExtension());

        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        return $container;
    }
}
