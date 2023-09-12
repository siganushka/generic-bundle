<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResourceNotFoundExceptionTest extends TestCase
{
    public function testAll(): void
    {
        $exception = new ResourceNotFoundException(12);
        static::assertInstanceOf(HttpException::class, $exception);
        static::assertSame('Resource #12 not found.', $exception->getMessage());
        static::assertSame(404, $exception->getStatusCode());

        $exception = new ResourceNotFoundException('siganushka');
        static::assertInstanceOf(HttpException::class, $exception);
        static::assertSame('Resource #siganushka not found.', $exception->getMessage());
        static::assertSame(404, $exception->getStatusCode());
    }
}
