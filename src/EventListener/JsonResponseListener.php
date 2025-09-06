<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class JsonResponseListener implements EventSubscriberInterface
{
    /**
     * @see https://www.laruence.com/2011/10/10/2239.html
     */
    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse && 0 === ($response->getEncodingOptions() & \JSON_UNESCAPED_UNICODE)) {
            $response->setEncodingOptions($response->getEncodingOptions() | \JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Fix JSON responses with 204 no content.
     *
     * [important] Must have a higher priority than "nelmio/cors-bundle" CourtListener::onKernelResponse.
     *
     * @see https://github.com/symfony/symfony/issues/29326
     * @see https://github.com/nodejs/node/issues/24580
     */
    public function onResponseForNoContent(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse && Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
            $event->setResponse(new Response(status: Response::HTTP_NO_CONTENT));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => [
                ['onResponse'],
                ['onResponseForNoContent', 255],
            ],
        ];
    }
}
