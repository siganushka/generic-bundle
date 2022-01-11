<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Utils;

use Symfony\Component\HttpFoundation\UrlHelper;

class PublicFileUtils
{
    protected $urlHelper;
    protected $publicDir;

    public function __construct(UrlHelper $urlHelper, string $publicDir)
    {
        $this->urlHelper = $urlHelper;
        $this->publicDir = realpath($publicDir);
    }

    /**
     * 获取文件访问 URL.
     *
     * @return string 文件访问 URL
     *
     * @throws \RuntimeException 获取文件路径失败或文件不存在
     */
    public function getUrl(\SplFileInfo $file): string
    {
        $path = $this->getPath($file);

        return $this->urlHelper->getAbsoluteUrl($path);
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
    public function getPath(\SplFileInfo $file): string
    {
        $path = $file->getRealPath();
        if (false === $path) {
            throw new \RuntimeException('File not found.');
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
