<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class SleepRequestListener implements EventSubscriberInterface
{
    public function __construct(private string $sleepParameter = '_sleep')
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        foreach ([$request->query, $request->request, $request->attributes] as $bag) {
            if ($bag->has($this->sleepParameter)) {
                sleep($bag->getInt($this->sleepParameter));
                break;
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', -8],
        ];
    }
}
