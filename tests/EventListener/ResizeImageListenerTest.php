<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Event\ResizeImageMaxHeightEvent;
use Siganushka\GenericBundle\Event\ResizeImageMaxWidthEvent;
use Siganushka\GenericBundle\EventListener\ResizeImageListener;

class ResizeImageListenerTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(\Imagick::class)) {
            static::markTestSkipped('Skip tests (Imagick not loaded).');
        }
    }

    public function testResizeImageMaxWidth(): void
    {
        $originFile = './tests/Mock/landscape.jpg';
        $targetFile = sprintf('./tests/Mock/landscape-%s.jpg', uniqid());

        if (!copy($originFile, $targetFile)) {
            static::markTestSkipped('Skip tests (Fail to copy file).');
        }

        $file = new \SplFileInfo($targetFile);

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(500, $width);
        static::assertSame(300, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxWidth(new ResizeImageMaxWidthEvent($file, 500));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(500, $width);
        static::assertSame(300, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxWidth(new ResizeImageMaxWidthEvent($file, 250));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(250, $width);
        static::assertSame(150, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxWidth(new ResizeImageMaxWidthEvent($file, 50));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(50, $width);
        static::assertSame(30, $height);

        unlink($targetFile);
    }

    public function testResizeImageMaxHeight(): void
    {
        $originFile = './tests/Mock/portrait.jpg';
        $targetFile = sprintf('./tests/Mock/portrait-%s.jpg', uniqid());

        if (!copy($originFile, $targetFile)) {
            static::markTestSkipped('Skip tests (Fail to copy file).');
        }

        $file = new \SplFileInfo($targetFile);

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(300, $width);
        static::assertSame(500, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxHeight(new ResizeImageMaxHeightEvent($file, 500));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(300, $width);
        static::assertSame(500, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxHeight(new ResizeImageMaxHeightEvent($file, 250));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(150, $width);
        static::assertSame(250, $height);

        $listener = new ResizeImageListener();
        $listener->onResizeImageMaxHeight(new ResizeImageMaxHeightEvent($file, 50));

        [$width, $height] = getimagesize($file->getPathname());
        static::assertSame(30, $width);
        static::assertSame(50, $height);

        unlink($targetFile);
    }
}
