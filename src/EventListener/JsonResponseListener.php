<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @see @see https://www.laruence.com/2011/10/10/2239.html
 */
class JsonResponseListener implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof JsonResponse) {
            $encodingOptions = $response->getEncodingOptions();
            /*
             * Using bitwise operators to check.
             *
             * @see https://www.laruence.com/2011/10/10/2239.html
             */
            if (0 === ($encodingOptions & \JSON_UNESCAPED_UNICODE)) {
                $response->setEncodingOptions($encodingOptions | \JSON_UNESCAPED_UNICODE);
            }

            /*
             * Fix JSON responses with no content.
             *
             * @see https://github.com/symfony/symfony/issues/29326
             * @see https://github.com/nodejs/node/issues/24580
             */
            if (Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
                $event->setResponse(new Response(status: Response::HTTP_NO_CONTENT));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponse',
        ];
    }
}
