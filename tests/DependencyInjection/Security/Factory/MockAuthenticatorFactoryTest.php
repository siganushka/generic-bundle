<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection\Security\Factory;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Security\Factory\MockAuthenticatorFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MockAuthenticatorFactoryTest extends TestCase
{
    public function testGetKeyAndPriority(): void
    {
        $factory = new MockAuthenticatorFactory();

        static::assertSame('mock', $factory->getKey());
        static::assertSame(-50, $factory->getPriority());
    }

    public function testAddConfiguration(): void
    {
        $node = new ArrayNodeDefinition('mock');
        (new MockAuthenticatorFactory())->addConfiguration($node);

        static::assertSame([
            'success_path' => '/',
            'failure_path' => '/',
            'identifier_parameter' => '_identifier',
        ], $node->getNode()->finalize([]));
    }

    public function testCreateAuthenticator(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('siganushka_generic.security.mock_authenticator', new Definition());

        $config = [
            'success_path' => '/home',
            'failure_path' => '/login',
            'identifier_parameter' => '_user',
        ];

        $authenticatorId = (new MockAuthenticatorFactory())
            ->createAuthenticator($container, 'main', $config, 'app_user_provider')
        ;

        static::assertSame('security.authenticator.mock.main', $authenticatorId);

        $definition = $container->getDefinition($authenticatorId);
        static::assertInstanceOf(ChildDefinition::class, $definition);
        static::assertSame('siganushka_generic.security.mock_authenticator', $definition->getParent());

        $userProvider = $definition->getArgument('$userProvider');
        static::assertInstanceOf(Reference::class, $userProvider);
        static::assertSame('app_user_provider', (string) $userProvider);

        static::assertSame($config, $definition->getArgument('$options'));
    }
}
