<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Symfony\Component\Serializer\Attribute\Ignore;

class Bar extends Foo
{
    public bool $testSnakeName = false;

    #[Ignore]
    public ?string $testIgnore = null;
}
