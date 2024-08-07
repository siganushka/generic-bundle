<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

/**
 * File utils.
 */
class FileUtils
{
    /**
     * 获取图像尺寸信息.
     *
     * @see https://www.php.net/manual/en/function.getimagesize.php
     *
     * @param \SplFileInfo $file 图像文件对象
     *
     * @return array{ 0: int, 1: int, 2: int, 3: string, bits?: int, channels?: int, mime: string } 图像尺寸信息
     *
     * @throws \RuntimeException 文件不存在或不是图像文件
     */
    public static function getImageSize(\SplFileInfo $file): array
    {
        if (!$file->isFile()) {
            throw new \RuntimeException('File not found.');
        }

        $result = @getimagesize($file->getPathname());
        if (false === $result) {
            throw new \RuntimeException('Unable to access file.');
        }

        return $result;
    }

    /**
     * 获取格式化后的文件大小.
     *
     * @see https://www.php.net/manual/zh/splfileinfo.getsize.php
     *
     * @param \SplFileInfo $file 文件对象
     *
     * @return string 格式化后的文件大小
     *
     * @throws \RuntimeException 获取文件大小失败或文件不存在
     */
    public static function getFormattedSize(\SplFileInfo $file): string
    {
        $size = $file->getSize();
        if (false === $size) {
            throw new \RuntimeException('Unable to access file.');
        }

        return static::formatBytes($size);
    }

    /**
     * 格式化字节数.
     *
     * @param int $bytes 字节数
     *
     * @return string 格式化字节数
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0B';
        }

        $base = log($bytes, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

        return round(1024 ** ($base - floor($base)), 2).($suffixes[(int) floor($base)] ?? '');
    }
}
