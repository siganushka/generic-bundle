<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Siganushka\GenericBundle\Controller\Crud\OperationsTrait;
use Siganushka\GenericBundle\Utils\ClassUtils;

trait WebOperationsTrait
{
    use OperationsTrait;

    protected function getControllerAlias(): string
    {
        return str_replace(['_controller', '_'], '', ClassUtils::generateAlias($this));
    }

    protected function getTemplateAlias(): string
    {
        return str_replace('_controller', '', ClassUtils::generateAlias($this));
    }
}
