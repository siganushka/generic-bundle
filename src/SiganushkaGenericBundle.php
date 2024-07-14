<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaGenericBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
