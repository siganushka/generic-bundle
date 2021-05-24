<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

interface ResourceInterface
{
    public function getId(): ?int;

    public function equals(?self $target): bool;
}
