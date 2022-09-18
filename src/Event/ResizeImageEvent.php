<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class ResizeImageEvent extends AbstractFileEvent
{
    private ?int $maxWidth = null;
    private ?int $maxHeight = null;

    public function __construct(\SplFileInfo $file, int $maxWidth = null, int $maxHeight = null)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;

        parent::__construct($file);
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }
}
