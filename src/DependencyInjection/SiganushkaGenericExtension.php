<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\EventListener\JsonUnicodeResponseListener;
use Siganushka\GenericBundle\Serializer\Encoder\JsonUnicodeEncoder;
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

        if (null !== $config['table_prefix']) {
            $container->register('siganushka_generic.doctrine.listener.table_prefix', TablePrefixListener::class)
                ->setArgument(0, $config['table_prefix'])
                ->addTag('doctrine.event_subscriber')
            ;
        }

        $container->register('siganushka_generic.listener.json_unicode_response', JsonUnicodeResponseListener::class)
            ->setArgument(0, $config['json_encode_options'])
            ->addTag('kernel.event_subscriber', ['priority' => 16])
        ;

        $container
            ->register('siganushka_generic.serializer.encoder.json_unicode', JsonUnicodeEncoder::class)
            ->setArgument(0, $config['json_encode_options'])
            ->addTag('serializer.encoder', ['priority' => 16])
        ;
    }
}
