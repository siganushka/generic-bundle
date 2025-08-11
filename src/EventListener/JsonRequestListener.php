<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestListener implements EventSubscriberInterface
{
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $isJSONRequest = str_contains($request->getRequestFormat() ?? '', 'json') || str_contains($request->getContentTypeFormat() ?? '', 'json');

        if ($isJSONRequest && \in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            try {
                $request->request = $request->getPayload();
            } catch (\Throwable) {
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
        ];
    }
}
