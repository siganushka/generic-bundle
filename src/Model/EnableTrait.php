<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait
{
    /**
     * @ORM\Column(type="boolean")
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
