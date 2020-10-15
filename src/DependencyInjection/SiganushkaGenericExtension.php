<?php

namespace Siganushka\GenericBundle\DependencyInjection;

use Siganushka\GenericBundle\Doctrine\EventSubscriber\TablePrefixSubscriber;
use Siganushka\GenericBundle\EventSubscriber\JsonResponseSubscriber;
use Siganushka\GenericBundle\Form\Extension\DisableHtml5ValidateTypeExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SiganushkaGenericExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (null !== $config['table_prefix']) {
            $container
                ->register(TablePrefixSubscriber::class)
                ->setArgument(0, $config['table_prefix'])
                ->addTag('doctrine.event_subscriber')
            ;
        }

        if ($config['disable_html5_validation']) {
            $container
                ->register(DisableHtml5ValidateTypeExtension::class)
                ->addTag('form.type_extension')
            ;
        }

        $container
            ->register(JsonResponseSubscriber::class)
            ->setArgument(0, $config['json_encode_options'])
            ->addTag('kernel.event_subscriber');
    }
}
