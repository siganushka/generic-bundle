<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestListener implements EventSubscriberInterface
{
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $contentType = $request->headers->get('CONTENT_TYPE');

        if (\is_string($contentType)
            && str_starts_with($contentType, 'application/json')
            && \in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])
        ) {
            try {
                $request->request = new InputBag($request->toArray());
            } catch (\Throwable $th) {
                // ignore...
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
