<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class JsonResponseListenerTest extends TestCase
{
    public function testAll(): void
    {
        $response = new JsonResponse(['message' => '你好！']);

        static::assertSame(0, $response->getEncodingOptions() & \JSON_UNESCAPED_UNICODE);
        static::assertSame('{"message":"\u4f60\u597d\uff01"}', $response->getContent());

        /** @var HttpKernelInterface */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $responseEvent = new ResponseEvent($kernel, new Request(), HttpKernelInterface::MAIN_REQUEST, $response);

        $listener = new JsonResponseListener();
        $listener->onResponse($responseEvent);

        static::assertSame(\JSON_UNESCAPED_UNICODE, $response->getEncodingOptions() & \JSON_UNESCAPED_UNICODE);
        static::assertSame('{"message":"你好！"}', $response->getContent());
    }
}
