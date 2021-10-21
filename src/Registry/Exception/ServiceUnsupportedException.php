<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Registry\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class ServiceUnsupportedException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $serviceId)
    {
        parent::__construct($registry, sprintf('Service %s for registry %s is unsupported.', $serviceId, \get_class($registry)));
    }
}
