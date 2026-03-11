<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Fixtures;

use Siganushka\GenericBundle\Controller\Crud\DeleteItemTrait;
use Siganushka\GenericBundle\Controller\Crud\GetCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\GetItemTrait;
use Siganushka\GenericBundle\Controller\Crud\PostCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\PutItemTrait;

class TestController
{
    use GetCollectionTrait;
    use PostCollectionTrait;
    use GetItemTrait;
    use PutItemTrait;
    use DeleteItemTrait;
}
