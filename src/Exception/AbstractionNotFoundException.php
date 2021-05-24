<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\Registry\RegistryInterface;

class AbstractionNotFoundException extends RegistryException
{
    public function __construct(RegistryInterface $registry, string $abstraction)
    {
        parent::__construct($registry, sprintf('Abstraction %s for %s could not be found.', $abstraction, \get_class($registry)));
    }
}
