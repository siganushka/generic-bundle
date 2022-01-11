<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Event\PublicFileDataEvent;
use Siganushka\GenericBundle\EventListener\PublicFileDataListener;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\RequestContext;

class PublicFileDataListenerTest extends TestCase
{
    private $urlHelper;

    protected function setUp(): void
    {
        $requestStack = new RequestStack();
        $requestContext = new RequestContext();
        $requestContext->setHost('example.com');

        $this->urlHelper = new UrlHelper($requestStack, $requestContext);
    }

    protected function tearDown(): void
    {
        $this->urlHelper = null;
    }

    public function testPublicFileDataListener(): void
    {
        $file = new \SplFileInfo('./tests/Mock/img.png');
        $event = new PublicFileDataEvent($file);
        static::assertSame($file, $event->getFile());
        static::assertSame([], $event->getData());

        $listener = new PublicFileDataListener($this->urlHelper, new PublicFileUtils('./tests'));
        $listener->onPublicFileData($event);

        $data = $event->getData();
        static::assertArrayHasKey('name', $data);
        static::assertArrayHasKey('path', $data);
        static::assertArrayHasKey('url', $data);
        static::assertArrayHasKey('size', $data);
        static::assertArrayHasKey('size_format', $data);
        static::assertArrayHasKey('extension', $data);

        static::assertSame('img.png', $data['name']);
        static::assertSame('/Mock/img.png', $data['path']);
        static::assertSame('http://example.com/Mock/img.png', $data['url']);
        static::assertSame('png', $data['extension']);
    }
}
