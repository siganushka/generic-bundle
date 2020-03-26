<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UuidResourceTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     *
     * @Groups({"resource"})
     */
    private $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return null === $this->id;
    }

    public function isEqualTo(?UuidResourceInterface $target): bool
    {
        if (null === $target) {
            return false;
        }

        return $this->id === $target->getId();
    }
}
