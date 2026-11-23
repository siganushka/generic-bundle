<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Security\Http\Authenticator;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Security\Http\Authenticator\MockAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class MockAuthenticatorTest extends TestCase
{
    public function testSupports(): void
    {
        $authenticator = $this->createAuthenticator();

        static::assertTrue($authenticator->supports(new Request(['_identifier' => 'foo'])));
        static::assertFalse($authenticator->supports(new Request()));

        $nonDebugAuthenticator = $this->createAuthenticator(false);
        static::assertFalse($nonDebugAuthenticator->supports(new Request(['_identifier' => 'foo'])));
    }

    public function testAuthenticateWithoutIdentifierException(): void
    {
        $this->expectException(BadCredentialsException::class);
        $this->expectExceptionMessage('The _identifier not found.');

        $this->createAuthenticator()->authenticate(new Request());
    }

    public function testAuthenticate(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->expects(static::once())
            ->method('loadUserByIdentifier')
            ->with('foo')
            ->willReturn($user)
        ;

        $passport = $this->createAuthenticator(userProvider: $userProvider)
            ->authenticate(new Request(['_identifier' => 'foo']))
        ;

        static::assertInstanceOf(SelfValidatingPassport::class, $passport);
        static::assertSame($user, $passport->getUser());
    }

    public function testOnAuthenticationSuccess(): void
    {
        $request = $this->createRequestWithSession();
        $response = new RedirectResponse('/');

        $httpUtils = $this->createMock(HttpUtils::class);
        $httpUtils->expects(static::once())
            ->method('createRedirectResponse')
            ->with($request, '/')
            ->willReturn($response)
        ;

        $authenticator = $this->createAuthenticator(httpUtils: $httpUtils);

        static::assertSame(
            $response,
            $authenticator->onAuthenticationSuccess($request, $this->createMock(TokenInterface::class), 'main')
        );
    }

    public function testOnAuthenticationSuccessWithTargetPath(): void
    {
        $request = $this->createRequestWithSession();
        $response = new RedirectResponse('/target');

        $authenticator = $this->createAuthenticator(httpUtils: $httpUtils = $this->createMock(HttpUtils::class));
        $request->getSession()->set('_security.main.target_path', '/target');

        $httpUtils->expects(static::once())
            ->method('createRedirectResponse')
            ->with($request, '/target')
            ->willReturn($response)
        ;

        static::assertSame(
            $response,
            $authenticator->onAuthenticationSuccess($request, $this->createMock(TokenInterface::class), 'main')
        );
    }

    public function testOnAuthenticationFailure(): void
    {
        $request = $this->createRequestWithSession();
        $response = new RedirectResponse('/');
        $exception = new AuthenticationException();

        $httpUtils = $this->createMock(HttpUtils::class);
        $httpUtils->expects(static::once())
            ->method('createRedirectResponse')
            ->with($request, '/')
            ->willReturn($response)
        ;

        $authenticator = $this->createAuthenticator(httpUtils: $httpUtils);

        static::assertSame($response, $authenticator->onAuthenticationFailure($request, $exception));
        static::assertSame(
            $exception,
            $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR)
        );
    }

    /**
     * @param UserProviderInterface<UserInterface>|null $userProvider
     */
    private function createAuthenticator(bool $debug = true, ?HttpUtils $httpUtils = null, ?UserProviderInterface $userProvider = null): MockAuthenticator
    {
        return new MockAuthenticator(
            $httpUtils ?? $this->createMock(HttpUtils::class),
            $userProvider ?? $this->createMock(UserProviderInterface::class),
            $debug,
        );
    }

    private function createRequestWithSession(): Request
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        return $request;
    }
}
