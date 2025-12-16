<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Twig\Extension;

use Siganushka\GenericBundle\Twig\Runtime\GenericExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GenericExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', [GenericExtensionRuntime::class, 'highlight'], ['is_safe' => ['html']]),
        ];
    }
}
