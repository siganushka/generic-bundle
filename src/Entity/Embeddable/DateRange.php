<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DateRange
{
    #[ORM\Column]
    private readonly ?\DateTimeImmutable $startAt;

    #[ORM\Column]
    private readonly ?\DateTimeImmutable $endAt;

    public function __construct(?\DateTimeImmutable $startAt = null, ?\DateTimeImmutable $endAt = null)
    {
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }
}
