<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle;

use Siganushka\GenericBundle\DependencyInjection\Compiler\DependencyCheckPass;
use Siganushka\GenericBundle\DependencyInjection\Security\Factory\MockAuthenticatorFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DependencyCheckPass());

        if ($container->hasExtension('security')) {
            /** @var SecurityExtension */
            $security = $container->getExtension('security');
            $security->addAuthenticatorFactory(new MockAuthenticatorFactory());
        }
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
