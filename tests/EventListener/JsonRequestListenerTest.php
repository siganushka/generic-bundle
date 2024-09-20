<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\EventListener\JsonRequestListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class JsonRequestListenerTest extends TestCase
{
    /**
     * @dataProvider requestProvider
     */
    public function testAll(string $method, array $server, ?string $content, array $parameter): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = Request::create('/', $method, [], [], [], $server, $content);

        $listener = new JsonRequestListener();
        $listener->onRequest(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));
        static::assertSame($parameter, $request->request->all());
    }

    public static function requestProvider(): array
    {
        return [
            ['GET', [], null, []],
            ['GET', [], '{"message":"\u4f60\u597d\uff01"}', []],
            ['GET', ['CONTENT_TYPE' => 'multipart/form-data'], '{"message":"\u4f60\u597d\uff01"}', []],
            ['GET', ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], '{"message":"\u4f60\u597d\uff01"}', []],
            ['GET', ['CONTENT_TYPE' => 'application/json'], '{"message":"\u4f60\u597d\uff01"}', []],
            ['POST', ['CONTENT_TYPE' => 'multipart/form-data'], '{"message":"\u4f60\u597d\uff01"}', []],
            ['POST', ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], '{"message":"\u4f60\u597d\uff01"}', []],
            ['POST', ['CONTENT_TYPE' => 'application/json'], '{"message":"\u4f60\u597d\uff01"}', ['message' => '你好！']],
            ['PUT', ['CONTENT_TYPE' => 'application/json'], '{"message":"\u4f60\u597d\uff01"}', ['message' => '你好！']],
            ['PATCH', ['CONTENT_TYPE' => 'application/json'], '{"message":"\u4f60\u597d\uff01"}', ['message' => '你好！']],
            ['DELETE', ['CONTENT_TYPE' => 'application/json'], '{"message":"\u4f60\u597d\uff01"}', ['message' => '你好！']],
        ];
    }
}
