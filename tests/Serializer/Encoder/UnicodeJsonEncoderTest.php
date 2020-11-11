<?php

namespace Siganushka\GenericBundle\Tests\Serializer\Encoder;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Encoder\UnicodeJsonEncoder;

class UnicodeJsonEncoderTest extends TestCase
{
    const FORMAT = 'json';

    public function testUnicodeJsonEncoder()
    {
        $encoder = new UnicodeJsonEncoder(JSON_UNESCAPED_UNICODE);

        $this->assertTrue($encoder->supportsEncoding(self::FORMAT));
        $this->assertEquals('{"message":"你好！"}', $encoder->encode(['message' => '你好！'], self::FORMAT));
    }
}
