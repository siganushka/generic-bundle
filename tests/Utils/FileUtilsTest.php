<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\FileUtils;

class FileUtilsTest extends TestCase
{
    /**
     * @dataProvider provideMockFiles
     */
    public function testGetRelativePath(string $rootDir, string $filename, string $filePathForPublic): void
    {
        $file = new \SplFileInfo($filename);

        $utils = new FileUtils($rootDir);
        static::assertSame($filePathForPublic, $utils->getRelativePath($file));
    }

    public function testGetRelativePathNonExisting()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to get file realpath');

        $file = new \SplFileInfo('./non_existing_file');

        $utils = new FileUtils('./');
        $utils->getRelativePath($file);
    }

    public function testGetRelativePathNonExistingForRootDir()
    {
        $rootDir = './tests';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('File not found for "%s"', realpath($rootDir)));

        $file = new \SplFileInfo('./composer.json');

        $utils = new FileUtils($rootDir);
        $utils->getRelativePath($file);
    }

    public function testGetFormattedSize(): void
    {
        $file = new \SplFileInfo('./tests/Mock/img.png');

        static::assertSame('143.52KB', FileUtils::getFormattedSize($file));
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

    public function provideMockFiles(): array
    {
        return [
            ['./tests/Utils', './tests/Utils/FileUtilsTest.php', '/FileUtilsTest.php'],
            ['./tests', './tests/Mock/img.png', '/Mock/img.png'],
            ['./', './composer.json', '/composer.json'],
        ];
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
