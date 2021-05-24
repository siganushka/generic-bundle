<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class RegistryException extends \RuntimeException
{
    protected $registry;

    public function __construct(RegistryInterface $registry, string $message, int $code = 0, \Throwable $previous = null)
    {
        $this->registry = $registry;

        parent::__construct($message, $code, $previous);
    }

    public function getRegistry()
    {
        return $this->registry;
    }
}
