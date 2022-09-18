<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\ResizeImageEvent;
use Siganushka\GenericBundle\Utils\FileUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResizeImageListener implements EventSubscriberInterface
{
    public function onResizeImage(ResizeImageEvent $event): void
    {
        if (!class_exists(\Imagick::class)) {
            return;
        }

        $file = $event->getFile();
        if ($maxWidth = $event->getMaxWidth()) {
            $this->resizeImageMaxWidth($file, $maxWidth);
        }

        if ($maxHeight = $event->getMaxHeight()) {
            $this->resizeImageMaxHeight($file, $maxHeight);
        }
    }

    private function resizeImageMaxWidth(\SplFileInfo $file, int $maxWidth): void
    {
        [$width, $height] = FileUtils::getImageSize($file);
        if ($width <= $maxWidth) {
            return;
        }

        $newHeight = (int) round($height * ($maxWidth / $width));

        $imagick = new \Imagick($file->getPathname());
        $imagick->resizeImage($maxWidth, $newHeight, \Imagick::FILTER_LANCZOS, 0.5);
        $imagick->writeImage($file->getPathname());
    }

    private function resizeImageMaxHeight(\SplFileInfo $file, int $maxHeight): void
    {
        [$width, $height] = FileUtils::getImageSize($file);
        if ($width <= $maxHeight) {
            return;
        }

        $newWidth = (int) round($width * ($maxHeight / $height));

        $imagick = new \Imagick($file->getPathname());
        $imagick->resizeImage($newWidth, $maxHeight, \Imagick::FILTER_LANCZOS, 0.5);
        $imagick->writeImage($file->getPathname());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResizeImageEvent::class => 'onResizeImage',
        ];
    }
}
