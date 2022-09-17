<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class ResizeImageMaxWidthEvent extends AbstractFileEvent
{
    private int $maxWidth;

    public function __construct(\SplFileInfo $file, int $maxWidth)
    {
        $this->maxWidth = $maxWidth;

        parent::__construct($file);
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }
}
