<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\RequestContext;

class PublicFileUtilsTest extends TestCase
{
    protected $urlHelper;

    protected function setUp(): void
    {
        $requestStack = new RequestStack();
        $requestContext = new RequestContext();

        $this->urlHelper = new UrlHelper($requestStack, $requestContext);
    }

    protected function tearDown(): void
    {
        $this->urlHelper = null;
    }

    /**
     * @dataProvider provideMockFiles
     */
    public function testGetUrl(string $publicDir, string $filePath, string $filePathForPublic): void
    {
        $file = new \SplFileInfo($filePath);
        $utils = new PublicFileUtils($this->urlHelper, $publicDir);

        static::assertSame('http://localhost'.$filePathForPublic, $utils->getUrl($file));
    }

    /**
     * @dataProvider provideMockFiles
     */
    public function testGetPath(string $publicDir, string $filePath, string $filePathForPublic): void
    {
        $file = new \SplFileInfo($filePath);
        $utils = new PublicFileUtils($this->urlHelper, $publicDir);

        static::assertSame($filePathForPublic, $utils->getPath($file));
    }

    public function testGetPathNonExisting()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found');

        $file = new \SplFileInfo('./non_existing_file');

        $utils = new PublicFileUtils($this->urlHelper, './');
        $utils->getPath($file);
    }

    public function testGetPathNonExistingForPublicDir()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found for public directory');

        $file = new \SplFileInfo('./composer.json');

        $utils = new PublicFileUtils($this->urlHelper, './tests');
        $utils->getPath($file);
    }

    public function provideMockFiles(): array
    {
        return [
            ['./tests/Utils', './tests/Utils/FileUtilsTest.php', '/FileUtilsTest.php'],
            ['./tests', './tests/Mock/landscape.jpg', '/Mock/landscape.jpg'],
            ['./tests', './tests/Mock/portrait.jpg', '/Mock/portrait.jpg'],
            ['./', './composer.json', '/composer.json'],
        ];
    }
}
