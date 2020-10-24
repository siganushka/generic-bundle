<?php

namespace Siganushka\GenericBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RegionFilterEvent extends Event
{
    /**
     * @var RegionInterface[]
     */
    private $regions = [];

    public function __construct(array $regions)
    {
        $this->regions = $regions;
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function setRegions(array $regions): void
    {
        $this->regions = $regions;
    }
}
