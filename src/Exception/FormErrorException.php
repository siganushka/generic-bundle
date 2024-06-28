<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormErrorException extends HttpException
{
    public function __construct(private FormInterface $form)
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Failed.');
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
