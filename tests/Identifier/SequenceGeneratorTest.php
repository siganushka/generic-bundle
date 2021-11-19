<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Identifier;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;

class SequenceGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new SequenceGenerator();
        $identifier = $generator->generate();

        static::assertNotEmpty($identifier);
        static::assertIsString($identifier);
        static::assertSame(16, mb_strlen($identifier));
    }
}
