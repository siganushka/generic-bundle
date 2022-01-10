<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\PublicFileDataEvent;
use Siganushka\GenericBundle\Utils\FileUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class PublicFileDataListener implements EventSubscriberInterface
{
    protected $urlHelper;
    protected $fileUtils;

    public function __construct(UrlHelper $urlHelper, FileUtils $fileUtils)
    {
        $this->urlHelper = $urlHelper;
        $this->fileUtils = $fileUtils;
    }

    public function onPublicFileData(PublicFileDataEvent $event)
    {
        $file = $event->getFile();
        $path = $this->fileUtils->getRelativePath($file);

        $data = [
            'name' => $file->getFilename(),
            'path' => $path,
            'url' => $this->urlHelper->getAbsoluteUrl($path),
            'size' => $file->getSize(),
            'size_format' => FileUtils::getFormattedSize($file),
            'extension' => $file->getExtension(),
        ];

        $event->setData($data)->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublicFileDataEvent::class => 'onPublicFileData',
        ];
    }
}
