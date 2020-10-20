<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait RegionSubjectTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\GenericBundle\Model\Region")
     *
     * @Groups({"region"})
     */
    private $province;

    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\GenericBundle\Model\Region")
     *
     * @Groups({"region"})
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\GenericBundle\Model\Region")
     *
     * @Groups({"region"})
     */
    private $district;

    public function getProvince(): ?RegionInterface
    {
        return $this->province;
    }

    public function setProvince(?RegionInterface $province): RegionSubjectInterface
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?RegionInterface
    {
        return $this->city;
    }

    public function setCity(?RegionInterface $city): RegionSubjectInterface
    {
        $this->city = $city;

        return $this;
    }

    public function getDistrict(): ?RegionInterface
    {
        return $this->district;
    }

    public function setDistrict(?RegionInterface $district): RegionSubjectInterface
    {
        $this->district = $district;

        return $this;
    }
}
