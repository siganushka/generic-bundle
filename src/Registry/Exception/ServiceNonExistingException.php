<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Registry\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class ServiceNonExistingException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $serviceId)
    {
        parent::__construct($registry, sprintf('Service %s for registry %s does not exist.', $serviceId, \get_class($registry)));
    }
}
