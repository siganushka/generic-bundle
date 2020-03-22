<?php

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class NonExistingServiceException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $serviceId)
    {
        parent::__construct($registry, sprintf('Service %s for registry %s does not exist.', $serviceId, \get_class($registry)));
    }
}
