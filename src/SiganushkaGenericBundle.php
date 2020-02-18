<?php

namespace Siganushka\GenericBundle;

use Siganushka\GenericBundle\DependencyInjection\Compiler\EntityListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityListenerPass());
    }
}
