<?php

namespace Siganushka\GenericBundle\Event;

use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RegionFilterEvent extends Event
{
    /**
     * @var RegionInterface[]
     */
    private $regions = [];

    public function __construct(array $regions)
    {
        foreach ($regions as $region) {
            if (!$region instanceof RegionInterface) {
                throw new \InvalidArgumentException(sprintf('Array of regions must be type of %s', RegionInterface::class));
            }

            array_push($this->regions, $region);
        }
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function setRegions(array $regions): void
    {
        $this->regions = array_values($regions);
    }
}
