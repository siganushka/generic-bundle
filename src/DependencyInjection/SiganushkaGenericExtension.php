<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Doctrine\ORM\Configuration as ORMConfiguration;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;

class SiganushkaGenericExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $currencyUtilsDef = $container->getDefinition('siganushka_generic.utils.currency');
        $currencyUtilsDef->setArgument(0, $config['currency']);

        if ($container::willBeAvailable('doctrine/orm', ORMConfiguration::class, ['siganushka/generic-bundle'])) {
            $loader->load('doctrine.php');

            if ($config['doctrine']['table_prefix']) {
                $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
                $tablePrefixDef->setArgument(0, $config['doctrine']['table_prefix']);
            } else {
                $container->removeDefinition('siganushka_generic.doctrine.listener.table_prefix');
            }
        }

        if ($container::willBeAvailable('symfony/form', Form::class, ['siganushka/generic-bundle'])) {
            $loader->load('form.php');

            if ($config['form']['html5_validation']) {
                $container->removeDefinition('siganushka_generic.form.type_extension.disable_html5_validation');
            }
        }

        if ($container::willBeAvailable('symfony/serializer', Serializer::class, ['siganushka/generic-bundle'])) {
            $loader->load('serializer.php');

            if (!$container::willBeAvailable('symfony/translation', Translator::class, ['siganushka/generic-bundle'])) {
                $container->removeDefinition('siganushka_generic.serializer.normalizer.translatable');
            }

            if (!$container::willBeAvailable('knplabs/knp-components', PaginatorInterface::class, ['siganushka/generic-bundle'])) {
                $container->removeDefinition('siganushka_generic.serializer.normalizer.knp_pagination');
            }
        }
    }
}
