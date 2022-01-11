<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\PublicFileDataEvent;
use Siganushka\GenericBundle\Utils\FileUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class PublicFileDataListener implements EventSubscriberInterface
{
    protected $urlHelper;
    protected $publicFileUtils;

    public function __construct(UrlHelper $urlHelper, PublicFileUtils $publicFileUtils)
    {
        $this->urlHelper = $urlHelper;
        $this->publicFileUtils = $publicFileUtils;
    }

    public function onPublicFileData(PublicFileDataEvent $event)
    {
        $file = $event->getFile();
        $path = $this->publicFileUtils->getPathForPublic($file);

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
