<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProblemJsonResponseTest extends TestCase
{
    public function testConstructor(): void
    {
        $response = new ProblemJsonResponse('test error', ProblemJsonResponse::HTTP_BAD_REQUEST);
        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertSame(ProblemJsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        static::assertSame('application/problem+json', $response->headers->get('Content-Type'));

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], json_decode($response->getContent() ?: '', true, \JSON_THROW_ON_ERROR));

        $response = new ProblemJsonResponse('test error', ProblemJsonResponse::HTTP_BAD_REQUEST, headers: ['Content-Type' => 'application/json']);
        static::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testCreateAsArray(): void
    {
        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], ProblemJsonResponse::createAsArray('test error', ProblemJsonResponse::HTTP_BAD_REQUEST));

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'Service Unavailable',
            'status' => 503,
            'detail' => 'test error',
        ], ProblemJsonResponse::createAsArray('test error', ProblemJsonResponse::HTTP_SERVICE_UNAVAILABLE));

        static::assertSame([
            'type' => 'about:blank',
            'title' => 'error title',
            'status' => 401,
            'detail' => 'test error',
        ], ProblemJsonResponse::createAsArray('test error', ProblemJsonResponse::HTTP_UNAUTHORIZED, title: 'error title'));

        static::assertSame([
            'type' => 'http://localhost',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'test error',
        ], ProblemJsonResponse::createAsArray('test error', ProblemJsonResponse::HTTP_BAD_REQUEST, type: 'http://localhost'));
    }

    public function testConstructorInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The status code must be a 4xx or 5xx');

        new ProblemJsonResponse('test error', 10);
    }

    public function testCreateAsArrayInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The status code must be a 4xx or 5xx');

        ProblemJsonResponse::createAsArray('test error', 1024);
    }
}
