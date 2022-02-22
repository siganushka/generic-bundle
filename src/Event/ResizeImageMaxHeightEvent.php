<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class ResizeImageMaxHeightEvent extends AbstractFileEvent
{
    protected int $maxHeight;

    public function __construct(\SplFileInfo $file, int $maxHeight)
    {
        $this->maxHeight = $maxHeight;

        parent::__construct($file);
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }
}
