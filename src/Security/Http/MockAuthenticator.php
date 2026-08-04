<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Security\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class MockAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    /**
     * @var array{
     *  success_path: string,
     *  failure_path: string,
     *  identifier_parameter: string
     * }
     */
    private array $options;

    /**
     * @param UserProviderInterface<UserInterface> $userProvider
     */
    public function __construct(
        private readonly HttpUtils $httpUtils,
        private readonly UserProviderInterface $userProvider,
        private readonly bool $debug,
        array $options = [])
    {
        $this->options = array_merge([
            'success_path' => '/',
            'failure_path' => '/',
            'identifier_parameter' => '_identifier',
        ], $options);
    }

    public function supports(Request $request): ?bool
    {
        return $this->debug && $request->query->has($this->options['identifier_parameter']);
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = $request->query->get($this->options['identifier_parameter'])
            ?? throw new BadCredentialsException(\sprintf('The %s not found.', $this->options['identifier_parameter']));

        return new SelfValidatingPassport(new UserBadge($identifier, $this->userProvider->loadUserByIdentifier(...)));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        return $this->httpUtils->createRedirectResponse($request, $targetPath ?? $this->options['success_path']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $session = $request->getSession();
        $session->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

        return $this->httpUtils->createRedirectResponse($request, $this->options['failure_path']);
    }
}
