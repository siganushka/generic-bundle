<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority higher than ResponseListener::onKernelResponse() in HttpKernel.
            // @see https://github.com/siganushka/generic-bundle/commit/ab8f74c5e73ae1319421c738ce8e2f70c7db427f
            KernelEvents::RESPONSE => ['onKernelResponse', 8],
        ];
    }
}
