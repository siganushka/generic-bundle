<?php

namespace Siganushka\GenericBundle\Model;

interface VersionableInterface
{
    public function getVersion(): ?int;

    public function setVersion(?int $version);
}
