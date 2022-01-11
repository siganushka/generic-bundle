<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class PublicFileDataEvent extends AbstractFileEvent
{
    protected $data = [];

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
