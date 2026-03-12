<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\ShowTrait;

class WebController
{
    use IndexTrait;
    use NewTrait;
    use EditTrait;
    use ShowTrait;
    use DeleteTrait;
}
