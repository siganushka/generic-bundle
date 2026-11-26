<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestFormatListener implements EventSubscriberInterface
{
    /**
     * @var list<array{0: RequestMatcherInterface, 1: string}>
     */
    private array $requestMatchers = [];

    public function addMatcher(RequestMatcherInterface $requestMatcher, string $format): void
    {
        $this->requestMatchers[] = [$requestMatcher, $format];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        foreach ($this->requestMatchers as [$requestMatcher, $format]) {
            if ($requestMatcher->matches($request)) {
                $request->setRequestFormat($format);

                return;
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority higher than RouterListener::onKernelRequest() in HttpKernel.
            KernelEvents::REQUEST => ['onKernelRequest', 34],
        ];
    }
}
