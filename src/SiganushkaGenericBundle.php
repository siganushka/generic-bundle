<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle;

use Siganushka\GenericBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineResolveTargetEntityPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
