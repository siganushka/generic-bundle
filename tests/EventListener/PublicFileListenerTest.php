<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Event\PublicFileEvent;
use Siganushka\GenericBundle\EventListener\PublicFileListener;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\RequestContext;

class PublicFileListenerTest extends TestCase
{
    private ?UrlHelper $urlHelper = null;

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

    public function testAll(): void
    {
        $file = new \SplFileInfo('./tests/Mock/landscape.jpg');
        $event = new PublicFileEvent($file);
        static::assertSame($file, $event->getFile());
        static::assertSame([], $event->getData());

        $listener = new PublicFileListener(new PublicFileUtils($this->urlHelper, './tests'));
        $listener->onPublicFile($event);

        $data = $event->getData();
        static::assertArrayHasKey('name', $data);
        static::assertArrayHasKey('path', $data);
        static::assertArrayHasKey('url', $data);
        static::assertArrayHasKey('size', $data);
        static::assertArrayHasKey('size_format', $data);
        static::assertArrayHasKey('extension', $data);

        static::assertSame('landscape.jpg', $data['name']);
        static::assertSame('/Mock/landscape.jpg', $data['path']);
        static::assertSame('http://localhost/Mock/landscape.jpg', $data['url']);
        static::assertSame('jpg', $data['extension']);
    }
}
