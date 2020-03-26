<?php

namespace Siganushka\GenericBundle\Model;

interface UuidResourceInterface
{
    public function getId(): ?string;

    public function isNew(): bool;

    public function isEqualTo(?self $target): bool;
}
