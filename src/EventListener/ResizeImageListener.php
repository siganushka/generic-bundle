<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\ResizeImageMaxHeightEvent;
use Siganushka\GenericBundle\Event\ResizeImageMaxWidthEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResizeImageListener implements EventSubscriberInterface
{
    /**
     * 按照最大宽度（宽不能超过 maxWidth）等比缩放.
     *
     * @param ResizeImageMaxWidthEvent $event 图像事件对象
     */
    public function onResizeImageMaxWidth(ResizeImageMaxWidthEvent $event): void
    {
        if (!class_exists(\Imagick::class)) {
            return;
        }

        $file = $event->getFile();
        $maxWidth = $event->getMaxWidth();

        [$width, $height] = $this->getImageSize($file);
        if ($width <= $maxWidth) {
            return;
        }

        $newHeight = (int) round($height * ($maxWidth / $width));

        $imagick = new \Imagick($file->getPathname());
        $imagick->resizeImage($maxWidth, $newHeight, \Imagick::FILTER_LANCZOS, 0.5);
        $imagick->writeImage($file->getPathname());
    }

    /**
     * 按照最大宽度（宽不能超过 maxWidth）等比缩放.
     *
     * @param ResizeImageMaxHeightEvent $event 图像事件对象
     */
    public function onResizeImageMaxHeight(ResizeImageMaxHeightEvent $event): void
    {
        if (!class_exists(\Imagick::class)) {
            return;
        }

        $file = $event->getFile();
        $maxHeight = $event->getMaxHeight();

        [$width, $height] = $this->getImageSize($file);
        if ($width <= $maxHeight) {
            return;
        }

        $newWidth = (int) round($width * ($maxHeight / $height));

        $imagick = new \Imagick($file->getPathname());
        $imagick->resizeImage($newWidth, $maxHeight, \Imagick::FILTER_LANCZOS, 0.5);
        $imagick->writeImage($file->getPathname());
    }

    /**
     * 获取图像尺寸信息.
     *
     * @param \SplFileInfo $file 图像文件对象
     *
     * @return array 图像尺寸信息
     *
     * @throws \RuntimeException 文件不存在或不是图像文件
     */
    private function getImageSize(\SplFileInfo $file): array
    {
        if (!$file->isFile()) {
            throw new \RuntimeException('File not found.');
        }

        $result = @getimagesize($file->getPathname());
        if (empty($result[0]) || empty($result[1])) {
            throw new \RuntimeException('Unable to access file.');
        }

        return $result;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResizeImageMaxWidthEvent::class => 'onResizeImageMaxWidth',
            ResizeImageMaxHeightEvent::class => 'onResizeImageMaxHeight',
        ];
    }
}
