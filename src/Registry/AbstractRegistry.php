<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Registry;

use Siganushka\GenericBundle\Registry\Exception\AbstractionNotFoundException;
use Siganushka\GenericBundle\Registry\Exception\ServiceExistingException;
use Siganushka\GenericBundle\Registry\Exception\ServiceNonExistingException;
use Siganushka\GenericBundle\Registry\Exception\ServiceUnsupportedException;

abstract class AbstractRegistry implements RegistryInterface
{
    /**
     * Services for registry.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Abstraction that services need to implement.
     *
     * @var string
     */
    private $abstraction;

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

    public function register(object $service): RegistryInterface
    {
        $serviceId = $this->getServiceId($service);
        if (!$service instanceof $this->abstraction) {
            throw new ServiceUnsupportedException($this, $serviceId);
        }

        if ($this->has($serviceId)) {
            throw new ServiceExistingException($this, $serviceId);
        }

        $this->services[$serviceId] = $service;

        return $this;
    }

    public function has(string $serviceId): bool
    {
        return \array_key_exists($serviceId, $this->services);
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

    public function getKeys(): array
    {
        return array_keys($this->services);
    }

    public function getValues(): array
    {
        return $this->services;
    }

    protected function getServiceId(object $service): string
    {
        if ($service instanceof AliasableInterface) {
            return $service->getAlias();
        }

        return \get_class($service);
    }
}
