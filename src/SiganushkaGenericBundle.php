<?php

namespace Siganushka\GenericBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $aliasMap = [
                'SiganushkaGenericBundle' => __NAMESPACE__.'\Model',
            ];

            $compilerPass = DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                array_values($aliasMap),
                [$this->getPath().'/Model'],
                [],
                false,
                $aliasMap,
            );

            $container->addCompilerPass($compilerPass);
        }
    }
}
