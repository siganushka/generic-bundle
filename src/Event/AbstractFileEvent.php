<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractFileEvent extends Event
{
    protected \SplFileInfo $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }
}
