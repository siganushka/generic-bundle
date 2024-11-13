<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Knp\Component\Pager\PaginatorInterface;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Form\Form;
use Symfony\Component\Serializer\Serializer;

class SiganushkaGenericExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('siganushka_generic.doctrine.table_prefix', $config['doctrine']['table_prefix']);
        $container->setParameter('siganushka_generic.doctrine.mapping_override', $config['doctrine']['mapping_override']);

        if ($container::willBeAvailable('siganushka/doctrine-contracts', ResourceInterface::class, ['siganushka/generic-bundle'])) {
            $loader->load('doctrine.php');

            if (!$config['doctrine']['mapping_override']) {
                $container->removeDefinition('siganushka_generic.doctrine.listener.mapping_override');
            }

            if (!$config['doctrine']['table_prefix']) {
                $container->removeDefinition('siganushka_generic.doctrine.listener.table_prefix');
            }
        }

        if ($container::willBeAvailable('symfony/form', Form::class, ['siganushka/generic-bundle'])) {
            $loader->load('form.php');

            if ($config['form']['html5_validation']) {
                $container->removeDefinition('siganushka_generic.form.type_extension.html5_validation');
            }
        }

        if ($container::willBeAvailable('symfony/serializer', Serializer::class, ['siganushka/generic-bundle'])) {
            $loader->load('serializer.php');

            if (!interface_exists(PaginatorInterface::class)) {
                $container->removeDefinition('siganushka_generic.serializer.normalizer.knp_pagination');
            }
        } else {
            $container->removeDefinition('siganushka_generic.listener.form_error');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        // @see https://symfony.com/doc/current/frontend/create_ux_bundle.html#specifics-for-asset-mapper
        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../../assets/dist' => '@siganushka/generic-bundle',
                    ],
                ],
            ]);
        }
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}
