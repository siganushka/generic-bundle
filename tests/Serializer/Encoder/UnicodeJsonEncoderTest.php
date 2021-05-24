<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Encoder;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Encoder\UnicodeJsonEncoder;

/**
 * @internal
 * @coversNothing
 */
final class UnicodeJsonEncoderTest extends TestCase
{
    public const FORMAT = 'json';

    public function testUnicodeJsonEncoder(): void
    {
        $encoder = new UnicodeJsonEncoder(JSON_UNESCAPED_UNICODE);

        static::assertTrue($encoder->supportsEncoding(self::FORMAT));
        static::assertSame('{"message":"你好！"}', $encoder->encode(['message' => '你好！'], self::FORMAT));
    }
}
