<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        $container->setParameter('siganushka_generic.doctrine.table_prefix', $config['doctrine']['table_prefix']);
        $container->setParameter('siganushka_generic.datetime.format', $config['datetime']['format']);
        $container->setParameter('siganushka_generic.datetime.timezone', $config['datetime']['timezone']);
        $container->setParameter('siganushka_generic.json.encoding_options', $config['json']['encoding_options']);
        $container->setParameter('siganushka_generic.currency.scale', $config['currency']['scale']);
        $container->setParameter('siganushka_generic.currency.grouping', $config['currency']['grouping']);
        $container->setParameter('siganushka_generic.currency.rounding_mode', $config['currency']['rounding_mode']);
        $container->setParameter('siganushka_generic.currency.divisor', $config['currency']['divisor']);

        if (null === $config['doctrine']['table_prefix']) {
            $container->removeDefinition('siganushka_generic.doctrine.listener.table_prefix');
        }
    }
}
