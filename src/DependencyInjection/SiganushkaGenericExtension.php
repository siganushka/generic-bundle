<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class SiganushkaGenericExtension extends Extension
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (null === $config['doctrine']['table_prefix']) {
            $container->removeDefinition('siganushka_generic.doctrine.listener.table_prefix');
        } else {
            $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
            $tablePrefixDef->setArgument(0, $config['doctrine']['table_prefix']);
        }

        if ($container->hasDefinition('siganushka_generic.serializer.encoder.json')) {
            $jsonEncodeDef = new Definition(JsonEncode::class);
            $jsonEncodeDef->setArgument(0, [JsonEncode::OPTIONS => $config['json']['encoding_options']]);

            $jsonEncoderDef = $container->getDefinition('siganushka_generic.serializer.encoder.json');
            $jsonEncoderDef->setArgument(0, $jsonEncodeDef);
        }

        $jsonResponseDef = $container->getDefinition('siganushka_generic.listener.json_response');
        $jsonResponseDef->setArgument(0, $config['json']['encoding_options']);

        $currencyUtilsDef = $container->getDefinition('siganushka_generic.utils.currency');
        $currencyUtilsDef->setArgument(0, [
            CurrencyUtils::DIVISOR => $config['currency']['divisor'],
            CurrencyUtils::DECIMALS => $config['currency']['decimals'],
            CurrencyUtils::DEC_POINT => $config['currency']['dec_point'],
            CurrencyUtils::THOUSANDS_SEP => $config['currency']['thousands_sep'],
        ]);
    }
}
