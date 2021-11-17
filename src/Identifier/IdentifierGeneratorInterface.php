<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Identifier;

interface IdentifierGeneratorInterface
{
    /**
     * 生成唯一标识.
     */
    public function generate(): string;
}
