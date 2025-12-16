<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class GenericExtensionRuntime implements RuntimeExtensionInterface
{
    public function highlight(string $subject, string $search, string $class = 'text-danger'): string
    {
        if (empty($search)) {
            return $subject;
        }

        $pattern = preg_quote(trim($search));
        $replacement = \sprintf('<strong class="%s">$0</strong>', htmlspecialchars($class));

        return preg_replace("/({$pattern})/ui", $replacement, $subject) ?? $subject;
    }
}
