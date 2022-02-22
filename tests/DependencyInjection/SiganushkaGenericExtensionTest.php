<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfigs([]);

        static::assertNull($container->getParameter('siganushka_generic.doctrine.table_prefix'));
        static::assertSame('Y-m-d H:i:s', $container->getParameter('siganushka_generic.datetime.format'));
        static::assertNull($container->getParameter('siganushka_generic.datetime.timezone'));
        static::assertSame(271, $container->getParameter('siganushka_generic.json.encoding_options'));
        static::assertSame(2, $container->getParameter('siganushka_generic.currency.scale'));
        static::assertTrue($container->getParameter('siganushka_generic.currency.grouping'));
        static::assertSame(\NumberFormatter::ROUND_HALFUP, $container->getParameter('siganushka_generic.currency.rounding_mode'));
        static::assertSame(100, $container->getParameter('siganushka_generic.currency.divisor'));

        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.public_file_data'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.resize_image'));
        static::assertTrue($container->hasDefinition('siganushka_generic.identifier.generator.sequence'));
        static::assertTrue($container->hasAlias(SequenceGenerator::class));
        static::assertTrue($container->hasDefinition('siganushka_generic.utils.public_file'));
        static::assertTrue($container->hasAlias(PublicFileUtils::class));
        static::assertTrue($container->hasDefinition('siganushka_generic.utils.currency'));
        static::assertTrue($container->hasAlias(CurrencyUtils::class));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.encoder.json'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.datetime'));

        $listenerTagAttributes = [
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ];

        $timestampableDef = $container->getDefinition('siganushka_generic.doctrine.listener.timestampable');
        $sortableDef = $container->getDefinition('siganushka_generic.doctrine.listener.sortable');
        static::assertSame(TimestampableListener::class, $timestampableDef->getClass());
        static::assertSame(SortableListener::class, $sortableDef->getClass());
        static::assertSame($listenerTagAttributes, $timestampableDef->getTag('doctrine.event_listener'));
        static::assertSame($listenerTagAttributes, $sortableDef->getTag('doctrine.event_listener'));

        $jsonResponseDef = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponseDef->hasTag('kernel.event_subscriber'));
        static::assertSame('%siganushka_generic.json.encoding_options%', $jsonResponseDef->getArgument(0));

        $publicFileDataDef = $container->getDefinition('siganushka_generic.listener.public_file_data');
        static::assertTrue($publicFileDataDef->hasTag('kernel.event_subscriber'));
        static::assertSame('siganushka_generic.utils.public_file', (string) $publicFileDataDef->getArgument(0));

        $resizeImageDef = $container->getDefinition('siganushka_generic.listener.resize_image');
        static::assertTrue($resizeImageDef->hasTag('kernel.event_subscriber'));

        $currencyDef = $container->getDefinition('siganushka_generic.utils.currency');
        static::assertSame('%siganushka_generic.currency.scale%', $currencyDef->getArgument(0));
        static::assertSame('%siganushka_generic.currency.grouping%', $currencyDef->getArgument(1));
        static::assertSame('%siganushka_generic.currency.rounding_mode%', $currencyDef->getArgument(2));
        static::assertSame('%siganushka_generic.currency.divisor%', $currencyDef->getArgument(3));

        $jsonEncoderDef = $container->getDefinition('siganushka_generic.serializer.encoder.json');
        static::assertTrue($jsonEncoderDef->hasTag('serializer.encoder'));
        static::assertSame([JsonEncode::OPTIONS => '%siganushka_generic.json.encoding_options%'], $jsonEncoderDef->getArgument(0)->getArgument(0));

        $datetimeNormalizerDef = $container->getDefinition('siganushka_generic.serializer.normalizer.datetime');
        static::assertTrue($datetimeNormalizerDef->hasTag('serializer.normalizer'));
        static::assertSame([
            DateTimeNormalizer::FORMAT_KEY => '%siganushka_generic.datetime.format%',
            DateTimeNormalizer::TIMEZONE_KEY => '%siganushka_generic.datetime.timezone%',
        ], $datetimeNormalizerDef->getArgument(0));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
            ],
            'datetime' => [
                'format' => 'm-d H:i',
                'timezone' => 'RPC',
            ],
            'json' => [
                'encoding_options' => 0,
            ],
            'currency' => [
                'scale' => 0,
                'grouping' => false,
                'divisor' => 1,
            ],
        ];

        $container = $this->createContainerWithConfigs([$configs]);

        static::assertSame('test_', $container->getParameter('siganushka_generic.doctrine.table_prefix'));
        static::assertSame('m-d H:i', $container->getParameter('siganushka_generic.datetime.format'));
        static::assertSame('RPC', $container->getParameter('siganushka_generic.datetime.timezone'));
        static::assertSame(0, $container->getParameter('siganushka_generic.json.encoding_options'));
        static::assertSame(0, $container->getParameter('siganushka_generic.currency.scale'));
        static::assertFalse($container->getParameter('siganushka_generic.currency.grouping'));
        static::assertSame(\NumberFormatter::ROUND_HALFUP, $container->getParameter('siganushka_generic.currency.rounding_mode'));
        static::assertSame(1, $container->getParameter('siganushka_generic.currency.divisor'));

        $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');

        static::assertSame(TablePrefixListener::class, $tablePrefixDef->getClass());
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixDef->getTag('doctrine.event_listener'));
    }

    /**
     * @param array<mixed> $configs
     */
    protected function createContainerWithConfigs(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaGenericExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
