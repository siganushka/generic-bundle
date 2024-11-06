<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class ResizeImageEvent extends AbstractFileEvent
{
    public function __construct(\SplFileInfo $file, private ?int $maxWidth = null, private ?int $maxHeight = null)
    {
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
