<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ResourceTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
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

    public function isEqual(?ResourceInterface $target): bool
    {
        if (null === $target) {
            return false;
        }

        return $this->id === $target->getId();
    }
}