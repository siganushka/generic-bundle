<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

class FileUtils
{
    protected $rootDir;

    public function __construct(string $rootDir)
    {
        $this->rootDir = realpath($rootDir);
    }

    /**
     * 获取文件相对于 $rootDir 目录的路径.
     *
     * @see https://www.php.net/manual/zh/splfileinfo.getrealpath.php
     *
     * @param \SplFileInfo $file 文件对象
     *
     * @return string 文件相对于 $rootDir 目录的路径
     *
     * @throws \RuntimeException 获取文件路径失败或文件不存在
     */
    public function getRelativePath(\SplFileInfo $file): string
    {
        $path = $file->getRealPath();
        if (false === $path) {
            throw new \RuntimeException('Unable to get file realpath.');
        }

        if (str_starts_with($path, $this->rootDir)) {
            $path = substr($path, \strlen($this->rootDir));
        }

        if (!is_file($this->rootDir.$path)) {
            throw new \RuntimeException(sprintf('File not found for "%s".', $this->rootDir));
        }

        return $path;
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
