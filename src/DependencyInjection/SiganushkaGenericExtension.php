<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\GenericBundle\Doctrine\Filter\DeletableFilter;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Serializer\Serializer;
use Twig\Environment;

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

            if (!$config['doctrine']['schema_resort']) {
                $container->removeDefinition('siganushka_generic.doctrine.schema_resort_listener');
                $container->removeDefinition('siganushka_generic.doctrine.schema_resort_command');
            }

            if (!$config['doctrine']['mapping_override']) {
                $container->removeDefinition('siganushka_generic.doctrine.mapping_override_listener');
            }

            if (!$config['doctrine']['table_prefix']) {
                $container->removeDefinition('siganushka_generic.doctrine.table_prefix_listener');
            }
        }

        if ($container::willBeAvailable('symfony/form', FormInterface::class, ['siganushka/generic-bundle'])) {
            $loader->load('form.php');

            if (!class_exists(Environment::class)) {
                $container->removeDefinition('siganushka_generic.form.controller');
            }

            if (!class_exists(Intl::class)) {
                $container->removeDefinition('siganushka_generic.form.money_type_extension');
            }
        }

        if ($container::willBeAvailable('symfony/serializer', Serializer::class, ['siganushka/generic-bundle'])) {
            $loader->load('serializer.php');

            if (!$config['serializer']['entity_normalizer'] || !interface_exists(ManagerRegistry::class)) {
                $container->removeDefinition('siganushka_generic.serializer.entity_class_metadata_factory');
                $container->removeDefinition('siganushka_generic.serializer.entity_normalizer');
            }

            if (!$config['serializer']['form_error_normalizer'] || !interface_exists(FormInterface::class)) {
                $container->removeDefinition('siganushka_generic.serializer.form_error_normalizer');
            }

            if (!$config['serializer']['knp_pagination_normalizer'] || !interface_exists(PaginatorInterface::class)) {
                $container->removeDefinition('siganushka_generic.serializer.knp_pagination_normalizer');
            }
        }

        $container->registerForAutoconfiguration(GenericEntityRepository::class)
            ->addTag('doctrine.repository_service')
        ;
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (self::isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../../assets/dist' => '@siganushka/generic-bundle',
                    ],
                ],
            ]);
        }

        if ($container::willBeAvailable('doctrine/orm', \Doctrine\ORM\Configuration::class, ['siganushka/generic-bundle'])) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'filters' => [
                        DeletableFilter::class => [
                            'class' => DeletableFilter::class,
                            'enabled' => true,
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * @see https://symfony.com/doc/current/frontend/create_ux_bundle.html#specifics-for-asset-mapper
     */
    public static function isAssetMapperAvailable(ContainerBuilder $container): bool
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
