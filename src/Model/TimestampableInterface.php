<?php

namespace Siganushka\GenericBundle\Model;

interface TimestampableInterface
{
    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt);

    public function getCreatedAt(): ?\DateTimeImmutable;

    public function setCreatedAt(\DateTimeImmutable $createdAt);
}
