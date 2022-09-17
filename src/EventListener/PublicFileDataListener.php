<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\PublicFileDataEvent;
use Siganushka\GenericBundle\Utils\FileUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublicFileDataListener implements EventSubscriberInterface
{
    private PublicFileUtils $publicFileUtils;

    public function __construct(PublicFileUtils $publicFileUtils)
    {
        $this->publicFileUtils = $publicFileUtils;
    }

    public function onPublicFileData(PublicFileDataEvent $event): void
    {
        $file = $event->getFile();

        $event->setData([
            'name' => $file->getFilename(),
            'path' => $this->publicFileUtils->getPath($file),
            'url' => $this->publicFileUtils->getUrl($file),
            'size' => $file->getSize(),
            'size_format' => FileUtils::getFormattedSize($file),
            'extension' => $file->getExtension(),
        ])->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublicFileDataEvent::class => 'onPublicFileData',
        ];
    }
}
