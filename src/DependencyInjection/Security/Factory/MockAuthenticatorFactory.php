<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MockAuthenticatorFactory implements AuthenticatorFactoryInterface
{
    public function getPriority(): int
    {
        return -50;
    }

    public function getKey(): string
    {
        return 'mock';
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    public function addConfiguration(NodeDefinition $node): void
    {
        $builder = $node->children();

        $builder
            ->scalarNode('success_path')
                ->defaultValue('/')
            ->end()
            ->scalarNode('failure_path')
                ->defaultValue('/')
            ->end()
            ->scalarNode('identifier_parameter')
                ->defaultValue('_identifier')
            ->end()
        ;
    }

    /**
     * @param array{
     *  success_path: string,
     *  failure_path: string,
     *  identifier_parameter: string
     * } $config
     */
    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string|array
    {
        $authenticatorId = \sprintf('security.authenticator.%s.%s', $this->getKey(), $firewallName);

        $container->setDefinition($authenticatorId, new ChildDefinition('siganushka_generic.security.mock_authenticator'))
            ->replaceArgument('$userProvider', new Reference($userProviderId))
            ->replaceArgument('$options', $config)
        ;

        return $authenticatorId;
    }
}
