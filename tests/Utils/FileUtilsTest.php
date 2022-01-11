<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\FileUtils;

class FileUtilsTest extends TestCase
{
    public function testGetFormattedSize(): void
    {
        $file = new \SplFileInfo('./tests/Mock/landscape.jpg');

        static::assertSame('50.01KB', FileUtils::getFormattedSize($file));
    }

    public function testGetFormattedSizeRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        FileUtils::getFormattedSize(new \SplFileInfo('./non_existing_file'));
    }

    /**
     * @dataProvider provideBytes
     */
    public function testFormatBytes(string $formatted, int $bytes): void
    {
        static::assertSame($formatted, FileUtils::formatBytes($bytes));
    }

    public function provideBytes(): array
    {
        return [
            ['0B', 0],
            ['0B', -1],
            ['1B', 1],
            ['1023B', 1023],
            ['1KB', 1024],
            ['64KB', 65535],
            ['64MB', 65535 * 1024],
            ['2GB', 2147483647],
            ['8EB', \PHP_INT_MAX],
        ];
    }
}
