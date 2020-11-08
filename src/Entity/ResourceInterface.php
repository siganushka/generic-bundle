<?php

namespace Siganushka\GenericBundle\Entity;

interface ResourceInterface
{
    public function getId(): ?int;

    public function isEqualTo(?self $target): bool;
}
