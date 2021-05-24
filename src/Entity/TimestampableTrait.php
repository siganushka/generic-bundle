<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"timestampable"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @Groups({"timestampable"})
     */
    private $createdAt;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
