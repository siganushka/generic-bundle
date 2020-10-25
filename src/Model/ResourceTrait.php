<?php

namespace Siganushka\GenericBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ResourceTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @Groups({"resource"})
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEqualTo(?ResourceInterface $target): bool
    {
        if (null === $target) {
            return false;
        }

        return $this->id === $target->getId();
    }
}
