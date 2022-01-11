<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

class PublicFileUtils
{
    protected $publicDir;

    public function __construct(string $publicDir)
    {
        $this->publicDir = realpath($publicDir);
    }

    public function getUrlForPublic()
    {
    }

    /**
     * 获取文件相对于 $publicDir 目录的路径.
     *
     * @see https://www.php.net/manual/zh/splfileinfo.getrealpath.php
     *
     * @param \SplFileInfo $file 文件对象
     *
     * @return string 文件相对于 $publicDir 目录的路径
     *
     * @throws \RuntimeException 获取文件路径失败或文件不存在
     */
    public function getPathForPublic(\SplFileInfo $file): string
    {
        $path = $file->getRealPath();
        if (false === $path) {
            throw new \RuntimeException('Unable to get realpath.');
        }

        if (str_starts_with($path, $this->publicDir)) {
            $path = substr($path, \strlen($this->publicDir));
        }

        if (!is_file($this->publicDir.$path)) {
            throw new \RuntimeException('File not found for public directory.');
        }

        return $path;
    }
}
