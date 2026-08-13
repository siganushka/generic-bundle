<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\ShowTrait;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/users', requirements: ['id' => Requirement::UUID_V7])]
class WebController
{
    use IndexTrait;
    use NewTrait;
    use ShowTrait;
    use EditTrait;
    use DeleteTrait;
}
