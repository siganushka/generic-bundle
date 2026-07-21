<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveByDependencyMappingPass implements CompilerPassInterface
{
    /**
     * @param array<string, string|array<string>> $dependencyMapping
     */
    public function __construct(private readonly array $dependencyMapping)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->dependencyMapping as $serviceId => $dependencyIds) {
            if (!$container->hasDefinition($serviceId)) {
                continue;
            }

            $dependencyIds = \is_string($dependencyIds) ? [$dependencyIds] : $dependencyIds;
            foreach ($dependencyIds as $dependencyId) {
                if (!$container->hasDefinition($dependencyId)) {
                    $container->removeDefinition($serviceId);
                    break;
                }
            }
        }
    }
}
