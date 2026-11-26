<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\EventListener\RequestFormatListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestFormatListenerTest extends TestCase
{
    public function testMatchingPatternForcesFormat(): void
    {
        $request = Request::create('/api/foo', server: ['HTTP_ACCEPT' => 'text/html']);
        static::assertSame('html', $request->getRequestFormat());

        $listener = $this->createListener([['^/api', 'json']]);
        $listener->onKernelRequest($this->createRequestEvent($request));

        static::assertSame('json', $request->getRequestFormat());
    }

    public function testRuleFormatIsAppliedPerMatcher(): void
    {
        $request = Request::create('/server/foo');

        $listener = $this->createListener([['^/api', 'json'], ['^/server', 'xml']]);
        $listener->onKernelRequest($this->createRequestEvent($request));

        static::assertSame('xml', $request->getRequestFormat());
    }

    public function testNonMatchingPatternKeepsDefaultFormat(): void
    {
        $request = Request::create('/admin/foo');

        $listener = $this->createListener([['^/api', 'json']]);
        $listener->onKernelRequest($this->createRequestEvent($request));

        static::assertSame('html', $request->getRequestFormat());
    }

    public function testFirstMatchingRuleWins(): void
    {
        $request = Request::create('/api/foo');

        $listener = $this->createListener([['^/api', 'json'], ['^/api/foo', 'xml']]);
        $listener->onKernelRequest($this->createRequestEvent($request));

        static::assertSame('json', $request->getRequestFormat());
    }

    public function testSubscribedEventPriorityIsHigherThanRouterListener(): void
    {
        // RouterListener listens to kernel.request with priority 32, the format must be set before routing.
        static::assertSame([
            KernelEvents::REQUEST => ['onKernelRequest', 34],
        ], RequestFormatListener::getSubscribedEvents());
    }

    public function testSubRequestIsIgnored(): void
    {
        $request = Request::create('/api/foo');

        $listener = $this->createListener([['^/api', 'json']]);
        $listener->onKernelRequest($this->createRequestEvent($request, HttpKernelInterface::SUB_REQUEST));

        static::assertSame('html', $request->getRequestFormat());
    }

    /**
     * @param list<array{0: string, 1: string}> $rules
     */
    private function createListener(array $rules): RequestFormatListener
    {
        $listener = new RequestFormatListener();

        foreach ($rules as [$pattern, $format]) {
            $listener->addMatcher(new PathRequestMatcher($pattern), $format);
        }

        return $listener;
    }

    private function createRequestEvent(Request $request, int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        return new RequestEvent($this->createMock(HttpKernelInterface::class), $request, $requestType);
    }
}
