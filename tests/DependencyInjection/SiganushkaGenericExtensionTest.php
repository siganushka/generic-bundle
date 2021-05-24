<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\SortableSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;
use Siganushka\GenericBundle\Doctrine\EventSubscriber\TimestampableSubscriber;
use Siganushka\GenericBundle\EventSubscriber\JsonResponseSubscriber;
use Siganushka\GenericBundle\Serializer\Encoder\UnicodeJsonEncoder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

        static::assertFalse($container->has(TablePrefixSubscriber::class));
        static::assertTrue($container->has(SortableSubscriber::class));
        static::assertTrue($container->has(TimestampableSubscriber::class));
        static::assertTrue($container->has(JsonResponseSubscriber::class));
        static::assertTrue($container->has(UnicodeJsonEncoder::class));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'table_prefix' => 'test_',
        ];

        $container = $this->createContainer();
        $container->loadFromExtension('siganushka_generic', $configs);
        $container->compile();

        static::assertTrue($container->has(TablePrefixSubscriber::class));
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
