<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

class Bar extends Foo
{
    public bool $testSnakeName = false;

    #[Ignore]
    public ?string $testIgnore = null;

    #[Groups('group_x')]
    public function getX(): string
    {
        return $this->x;
    }

    public function getCustom(): string
    {
        return 'hello';
    }

    #[Groups('group_custom')]
    public function getCustomWithGroups(): string
    {
        return 'hello';
    }
}
