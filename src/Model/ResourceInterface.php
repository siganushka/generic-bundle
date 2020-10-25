<?php

namespace Siganushka\GenericBundle\Model;

interface ResourceInterface
{
    public function getId(): ?int;

    public function isEqualTo(?self $target): bool;
}
