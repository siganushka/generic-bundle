<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @see https://www.laruence.com/2011/10/10/2239.html
 */
class JsonResponseListener implements EventSubscriberInterface
{
    private int $jsonEncodeOptions;

    public function __construct(int $jsonEncodeOptions)
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions($this->jsonEncodeOptions);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => 'onResponse'];
    }
}
