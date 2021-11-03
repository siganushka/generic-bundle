<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SiganushkaGenericExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('siganushka_generic.table_prefix', $config['table_prefix']);
        $container->setParameter('siganushka_generic.datetime_format', $config['datetime_format']);
        $container->setParameter('siganushka_generic.datetime_timezone', $config['datetime_timezone']);
        $container->setParameter('siganushka_generic.json_encode_options', $config['json_encode_options']);
        // dd($container->getParameterBag()->all());

        if (null === $config['table_prefix']) {
            $container->removeDefinition('siganushka_generic.doctrine.listener.table_prefix');
        }
    }
}
