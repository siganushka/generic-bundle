<?php

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TimestampableSubscriber;
use Siganushka\GenericBundle\EventSubscriber\JsonResponseSubscriber;
use Siganushka\GenericBundle\Form\Extension\DisableHtml5ValidateTypeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig()
    {
        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_generic');
        $container->compile();

        $this->assertFalse($container->has(TablePrefixSubscriber::class));
        $this->assertTrue($container->has(SortableSubscriber::class));
        $this->assertTrue($container->has(TimestampableSubscriber::class));
        $this->assertTrue($container->has(JsonResponseSubscriber::class));
        $this->assertTrue($container->has(DisableHtml5ValidateTypeExtension::class));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'table_prefix' => 'test_',
            'disable_html5_validation' => false,
        ];

        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_generic', $configs);
        $container->compile();

        $this->assertTrue($container->has(TablePrefixSubscriber::class));
        $this->assertFalse($container->has(DisableHtml5ValidateTypeExtension::class));
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
