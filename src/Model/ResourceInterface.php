<?php

namespace Siganushka\GenericBundle\Model;

interface ResourceInterface
{
    public function getId(): ?int;

    public function isNew(): bool;

    public function isEqualTo(?self $target): bool;
}
