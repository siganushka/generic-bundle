<?php

namespace Siganushka\GenericBundle\DependencyInjection;

use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;
use Siganushka\GenericBundle\EventSubscriber\JsonResponseSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SiganushkaGenericExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('generic.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (null === $config['table_prefix']) {
            $container->removeDefinition(TablePrefixSubscriber::class);
        } else {
            $container->findDefinition(TablePrefixSubscriber::class)
                ->setArgument(0, $config['table_prefix']);
        }

        if (!$config['unescaped_unicode_json_response']) {
            $container->removeDefinition(JsonResponseSubscriber::class);
        }
    }
}
