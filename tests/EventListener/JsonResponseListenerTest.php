<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\EventListener\JsonResponseListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 * @coversNothing
 */
final class JsonResponseListenerTest extends TestCase
{
    public function testJsonResponseListener(): void
    {
        $response = new JsonResponse(['message' => '你好！']);

        static::assertSame('{"message":"\u4f60\u597d\uff01"}', $response->getContent());

        $requestType = \defined(sprintf('%s::MAIN_REQUEST', HttpKernelInterface::class))
            ? HttpKernelInterface::MAIN_REQUEST
            : HttpKernelInterface::MASTER_REQUEST;

        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $responseEvent = new ResponseEvent($httpKernel, new Request(), $requestType, $response);

        $listener = new JsonResponseListener(\JSON_UNESCAPED_UNICODE);
        $listener->onResponse($responseEvent);

        static::assertSame('{"message":"你好！"}', $response->getContent());
    }
}
