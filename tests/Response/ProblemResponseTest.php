<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Response\ProblemResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

class ProblemResponseTest extends TestCase
{
    public function testConstructor(): void
    {
        $response = new ProblemResponse('test error', ProblemResponse::HTTP_BAD_REQUEST);
        assertInstanceOf(JsonResponse::class, $response);
        assertSame(ProblemResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], json_decode($response->getContent() ?: '', true, \JSON_THROW_ON_ERROR));
    }

    public function testCreateAsArray(): void
    {
        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], ProblemResponse::createAsArray('test error', ProblemResponse::HTTP_BAD_REQUEST));

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Service Unavailable',
            'status' => 503,
            'detail' => 'test error',
        ], ProblemResponse::createAsArray('test error', ProblemResponse::HTTP_SERVICE_UNAVAILABLE));

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'error title',
            'status' => 401,
            'detail' => 'test error',
        ], ProblemResponse::createAsArray('test error', ProblemResponse::HTTP_UNAUTHORIZED, title: 'error title'));

        static::assertSame([
            'type' => 'http://localhost',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], ProblemResponse::createAsArray('test error', ProblemResponse::HTTP_BAD_REQUEST, type: 'http://localhost'));
    }

    public function testConstructorInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The status code must be a 4xx or 5xx');

        new ProblemResponse('test error', 10);
    }

    public function testCreateAsArrayInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The status code must be a 4xx or 5xx');

        ProblemResponse::createAsArray('test error', 1024);
    }
}
