<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PublicFileDataEvent extends Event
{
    protected $file;
    protected $data = [];

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
