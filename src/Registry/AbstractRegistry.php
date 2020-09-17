<?php

namespace Siganushka\GenericBundle\Registry;

use Siganushka\GenericBundle\Exception\AbstractionNotFoundException;
use Siganushka\GenericBundle\Exception\ServiceExistingException;
use Siganushka\GenericBundle\Exception\ServiceNonExistingException;
use Siganushka\GenericBundle\Exception\ServiceUnsupportedException;

abstract class AbstractRegistry implements RegistryInterface
{
    /**
     * Abstraction that services need to implement.
     *
     * @var string
     */
    private $abstraction;

    /**
     * Services for registry.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Abstraction interface for construct.
     *
     * @throws AbstractionNotFoundException
     */
    public function __construct(string $abstraction)
    {
        if (!interface_exists($abstraction)) {
            throw new AbstractionNotFoundException($this, $abstraction);
        }

        $this->abstraction = $abstraction;
    }

    public function register(object $service): void
    {
        $serviceId = $this->getServiceId($service);
        if (!$service instanceof $this->abstraction) {
            throw new ServiceUnsupportedException($this, $serviceId);
        }

        if ($this->has($serviceId)) {
            throw new ServiceExistingException($this, $serviceId);
        }

        $this->services[$serviceId] = $service;
    }

    public function has(string $serviceId): bool
    {
        return isset($this->services[$serviceId]);
    }

    public function get(string $serviceId): object
    {
        if (!$this->has($serviceId)) {
            throw new ServiceNonExistingException($this, $serviceId);
        }

        return $this->services[$serviceId];
    }

    public function remove(string $serviceId): void
    {
        if (!$this->has($serviceId)) {
            throw new ServiceNonExistingException($this, $serviceId);
        }

        unset($this->services[$serviceId]);
    }

    public function clear(): void
    {
        $this->services = [];
    }

    public function keys(): array
    {
        return array_keys($this->services);
    }

    public function values(): array
    {
        return $this->services;
    }

    protected function getServiceId($service)
    {
        if ($service instanceof AliasableInterface) {
            return $service->getAlias();
        }

        return \get_class($service);
    }
}
