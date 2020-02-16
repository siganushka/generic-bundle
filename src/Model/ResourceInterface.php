<?php

namespace Siganushka\GenericBundle\Model;

interface ResourceInterface
{
    public function getId(): ?string;

    public function isNew(): bool;

    public function isEqual(?self $target): bool;
}
