<?php

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class ServiceExistingException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $serviceId)
    {
        parent::__construct($registry, sprintf('Service %s for registry %s already exists.', $serviceId, \get_class($registry)));
    }
}
