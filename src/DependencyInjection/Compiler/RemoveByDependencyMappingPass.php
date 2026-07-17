<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveByDependencyMappingPass implements CompilerPassInterface
{
    public function __construct(private readonly array $dependencyMapping)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->dependencyMapping as $serviceId => $dependencyId) {
            if (!$container->hasDefinition($dependencyId)) {
                $container->removeDefinition($serviceId);
            }
        }
    }
}
