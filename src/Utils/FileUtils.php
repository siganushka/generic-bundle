<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

class FileUtils
{
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
        $bytes = $file->getSize();
        if (false === $bytes) {
            throw new \RuntimeException('Unable to get file size.');
        }

        return static::formatBytes($bytes);
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

        return round(1024 ** ($base - floor($base)), 2).$suffixes[floor($base)];
    }
}
