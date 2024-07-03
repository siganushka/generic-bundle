<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Exception\FormErrorException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormErrorListener implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.form_error')]
        private readonly NormalizerInterface $normalizer,
        #[Autowire(service: 'translator')]
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if (!$throwable instanceof FormErrorException) {
            return;
        }

        $formErrors = $this->normalizer->normalize($throwable->getForm());
        $statusCode = $throwable->getStatusCode();

        $data = [
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => Response::$statusTexts[$statusCode] ?? 'An error occurred',
            'status' => $statusCode,
            'detail' => $formErrors['errors'][0]['message'] ?? $this->translator->trans($throwable->getMessage(), [], 'validators'),
            'errors' => $formErrors['children'] ?? [],
        ];

        $event->setResponse(new JsonResponse($data));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onException',
        ];
    }
}
