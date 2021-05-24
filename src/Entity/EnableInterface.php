<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

interface EnableInterface
{
    public function isEnabled(): ?bool;

    public function setEnabled(?bool $enabled);
}
