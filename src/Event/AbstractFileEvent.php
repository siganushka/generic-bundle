<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractFileEvent extends Event
{
    public function __construct(protected \SplFileInfo $file)
    {
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }
}
