<?php

namespace Siganushka\GenericBundle\Entity;

interface RegionSubjectInterface
{
    public function getProvince(): ?RegionInterface;

    public function setProvince(?RegionInterface $province): self;

    public function getCity(): ?RegionInterface;

    public function setCity(?RegionInterface $city): self;

    public function getDistrict(): ?RegionInterface;

    public function setDistrict(?RegionInterface $district): self;

    public function getRegionAsString(): string;
}
