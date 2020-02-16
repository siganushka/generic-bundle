<?php

namespace Siganushka\GenericBundle\Model;

interface EnableInterface
{
    public function isEnabled(): ?bool;

    public function setEnabled(bool $enabled);
}
