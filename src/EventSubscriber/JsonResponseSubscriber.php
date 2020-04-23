<?php

namespace Siganushka\GenericBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class JsonResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [ResponseEvent::class => 'onResponseEvent'];
    }

    public function onResponseEvent(ResponseEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof JsonResponse) {
            return;
        }

        /**
         * @see https://www.laruence.com/2011/10/10/2239.html
         */
        $defaults = $response->getEncodingOptions();
        $response->setEncodingOptions($defaults | JSON_UNESCAPED_UNICODE);
    }
}
