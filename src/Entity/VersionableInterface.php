<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

interface VersionableInterface
{
    public function getVersion(): ?int;

    public function setVersion(?int $version);
}
