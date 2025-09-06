<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonResponseListener implements EventSubscriberInterface
{
    /**
     * @see https://www.laruence.com/2011/10/10/2239.html
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse && 0 === ($response->getEncodingOptions() & \JSON_UNESCAPED_UNICODE)) {
            $response->setEncodingOptions($response->getEncodingOptions() | \JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @see https://github.com/symfony/symfony/issues/29326
     * @see https://github.com/nodejs/node/issues/24580
     */
    public function onKernelResponseForNoContent(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse && Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
            $event->setResponse(new Response(status: Response::HTTP_NO_CONTENT));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                ['onKernelResponse'],
                // priority higher than "nelmio/cors-bundle" CourtListener::onKernelResponse.
                ['onKernelResponseForNoContent', 10],
            ],
        ];
    }
}
