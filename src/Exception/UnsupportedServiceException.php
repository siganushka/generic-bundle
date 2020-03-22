<?php

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class UnsupportedServiceException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $serviceId)
    {
        parent::__construct($registry, sprintf('Service %s for registry %s is unsupported..', $serviceId, \get_class($registry)));
    }
}
