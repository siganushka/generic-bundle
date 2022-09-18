<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\EventListener;

use Siganushka\GenericBundle\Event\PublicFileEvent;
use Siganushka\GenericBundle\Utils\FileUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublicFileListener implements EventSubscriberInterface
{
    private PublicFileUtils $publicFileUtils;

    public function __construct(PublicFileUtils $publicFileUtils)
    {
        $this->publicFileUtils = $publicFileUtils;
    }

    public function onPublicFile(PublicFileEvent $event): void
    {
        $file = $event->getFile();
        $path = $this->publicFileUtils->getPath($file);
        $url = $this->publicFileUtils->getUrl($file);

        $event->setData([
            'name' => $file->getFilename(),
            'path' => $path,
            'url' => $url,
            'size' => $file->getSize(),
            'size_format' => FileUtils::getFormattedSize($file),
            'extension' => $file->getExtension(),
        ])->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublicFileEvent::class => 'onPublicFile',
        ];
    }
}
