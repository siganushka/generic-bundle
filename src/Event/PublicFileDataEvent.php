<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Event;

class PublicFileDataEvent extends AbstractFileEvent
{
    /**
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<mixed> $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
