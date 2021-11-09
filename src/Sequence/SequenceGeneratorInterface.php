<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Sequence;

interface SequenceGeneratorInterface
{
    public function generate(): string;
}
