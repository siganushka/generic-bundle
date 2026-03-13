<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud\Web;

use Siganushka\GenericBundle\Controller\Crud\OperationsTrait;
use Siganushka\GenericBundle\Utils\ClassUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

trait WebOperationsTrait
{
    use OperationsTrait;

    protected function addFlashMessage(Request $request, string $type, mixed $message): void
    {
        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add($type, $message);
        }
    }

    protected function getControllerAlias(): string
    {
        return str_replace(['_controller', '_'], '', ClassUtils::generateAlias($this));
    }

    protected function getTemplateAlias(): string
    {
        return str_replace('_controller', '', ClassUtils::generateAlias($this));
    }
}
