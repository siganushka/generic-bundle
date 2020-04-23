<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait VersionableTrait
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Version()
     *
     * @Groups({"versionable"})
     */
    private $version;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version)
    {
        $this->version = $version;

        return $this;
    }
}
