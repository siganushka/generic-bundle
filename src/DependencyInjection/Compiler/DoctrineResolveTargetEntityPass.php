<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')) {
            return;
        }

        /** @var array<string, string> */
        $mappingOverride = $container->getParameter('siganushka_generic.doctrine.mapping_override');
        if (0 === \count($mappingOverride)) {
            return;
        }

        // @see https://github.com/doctrine/DoctrineBundle/blob/2.13.x/src/DependencyInjection/DoctrineExtension.php#L643
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        // If "doctrine.orm.resolve_target_entities" is not set, there is no "doctrine.event_listener" tag
        if (!$definition->hasTag('doctrine.event_listener')) {
            $definition->addTag('doctrine.event_listener', ['event' => Events::loadClassMetadata]);
            $definition->addTag('doctrine.event_listener', ['event' => Events::onClassMetadataNotFound]);
        }

        foreach ($mappingOverride as $source => $target) {
            $definition->addMethodCall('addResolveTargetEntity', [$source, $target, []]);
        }
    }
}
