<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class JsonResponseSubscriber implements EventSubscriberInterface
{
    private $jsonEncodeOptions;

    public function __construct(int $jsonEncodeOptions)
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public static function getSubscribedEvents()
    {
        return [ResponseEvent::class => 'onResponseEvent'];
    }

    public function onResponseEvent(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse) {
            // @see https://www.laruence.com/2011/10/10/2239.html
            $response->setEncodingOptions($this->jsonEncodeOptions);
        }
    }
}
