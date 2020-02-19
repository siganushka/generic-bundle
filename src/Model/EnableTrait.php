<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait EnableTrait
{
    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"enable"})
     */
    private $enabled;

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
