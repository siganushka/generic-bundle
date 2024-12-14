<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig([]);

        static::assertSame($container->getParameter('siganushka_generic.doctrine.table_prefix'), null);
        static::assertSame($container->getParameter('siganushka_generic.doctrine.mapping_override'), []);

        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_request'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.form_error'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.mapping_override'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertFalse($container->hasDefinition('siganushka_generic.form.type_extension.html5_validation'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.knp_pagination'));

        $jsonRequest = $container->getDefinition('siganushka_generic.listener.json_request');
        static::assertTrue($jsonRequest->hasTag('kernel.event_subscriber'));

        $jsonResponse = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponse->hasTag('kernel.event_subscriber'));

        $formError = $container->getDefinition('siganushka_generic.listener.form_error');
        static::assertTrue($formError->hasTag('kernel.event_subscriber'));

        $listenerTagAttributes = [
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ];

        $timestampable = $container->getDefinition('siganushka_generic.doctrine.listener.timestampable');
        static::assertSame($listenerTagAttributes, $timestampable->getTag('doctrine.event_listener'));

        $knpPagination = $container->getDefinition('siganushka_generic.serializer.normalizer.knp_pagination');
        static::assertTrue($knpPagination->hasTag('serializer.normalizer'));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
                'mapping_override' => [
                    Foo::class => Bar::class,
                ],
            ],
            'form' => [
                'html5_validation' => false,
            ],
        ];

        $container = $this->createContainerWithConfig($configs);

        static::assertSame($container->getParameter('siganushka_generic.doctrine.table_prefix'), $configs['doctrine']['table_prefix']);
        static::assertSame($container->getParameter('siganushka_generic.doctrine.mapping_override'), $configs['doctrine']['mapping_override']);

        $tablePrefix = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefix->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.table_prefix%', $tablePrefix->getArgument(0));

        $mappingOverride = $container->getDefinition('siganushka_generic.doctrine.listener.mapping_override');
        static::assertSame([['event' => 'loadClassMetadata']], $mappingOverride->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.mapping_override%', $mappingOverride->getArgument(0));

        $html5Validation = $container->getDefinition('siganushka_generic.form.type_extension.html5_validation');
        static::assertTrue($html5Validation->hasTag('form.type_extension'));
    }

    private function createContainerWithConfig(array $config = []): ContainerBuilder
    {
        $extension = new SiganushkaGenericExtension();

        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias(), $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
