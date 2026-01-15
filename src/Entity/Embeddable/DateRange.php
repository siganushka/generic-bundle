<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DateRange
{
    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    public function __construct(?\DateTimeInterface $startAt = null, ?\DateTimeImmutable $endAt = null)
    {
        $this->setStartAt($startAt);
        $this->setEndAt($endAt);
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): static
    {
        $startAt && $this->startAt = \DateTimeImmutable::createFromInterface($startAt);

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): static
    {
        $endAt && $this->endAt = \DateTimeImmutable::createFromInterface($endAt);

        return $this;
    }
}
