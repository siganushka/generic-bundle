<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\EventSubscriber\JsonResponseSubscriber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 * @coversNothing
 */
final class JsonResponseSubscriberTest extends TestCase
{
    public function testCustomEncodeOptions(): void
    {
        $response = new JsonResponse(['message' => '你好！']);

        static::assertSame('{"message":"\u4f60\u597d\uff01"}', $response->getContent());

        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $responseEvent = new ResponseEvent($httpKernel, new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JsonResponseSubscriber(JSON_UNESCAPED_UNICODE);
        $listener->onResponseEvent($responseEvent);

        static::assertSame('{"message":"你好！"}', $response->getContent());
    }
}
