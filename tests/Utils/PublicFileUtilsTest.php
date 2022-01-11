<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\PublicFileUtils;

class PublicFileUtilsTest extends TestCase
{
    /**
     * @dataProvider provideMockFiles
     */
    public function testGetPathForPublic(string $publicDir, string $filePath, string $filePathForPublic): void
    {
        $file = new \SplFileInfo($filePath);

        $utils = new PublicFileUtils($publicDir);
        static::assertSame($filePathForPublic, $utils->getPathForPublic($file));
    }

    public function testGetPathForPublicNonExisting()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to get realpath');

        $file = new \SplFileInfo('./non_existing_file');

        $utils = new PublicFileUtils('./');
        $utils->getPathForPublic($file);
    }

    public function testGetPathForPublicNonExistingForPublicDir()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found for public directory');

        $file = new \SplFileInfo('./composer.json');

        $utils = new PublicFileUtils('./tests');
        $utils->getPathForPublic($file);
    }

    public function provideMockFiles(): array
    {
        return [
            ['./tests/Utils', './tests/Utils/FileUtilsTest.php', '/FileUtilsTest.php'],
            ['./tests', './tests/Mock/img.png', '/Mock/img.png'],
            ['./', './composer.json', '/composer.json'],
        ];
    }
}
