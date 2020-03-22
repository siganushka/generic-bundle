<?php

namespace Siganushka\GenericBundle\Registry;

use Siganushka\GenericBundle\Exception\ExistingServiceException;
use Siganushka\GenericBundle\Exception\NonExistingServiceException;
use Siganushka\GenericBundle\Exception\UnsupportedServiceException;

interface RegistryInterface
{
    /**
     * The service for registry.
     *
     * @throws UnsupportedServiceException
     * @throws ExistingServiceException
     */
    public function register(object $service): void;

    /**
     * Check service if exists.
     */
    public function has(string $serviceId): bool;

    /**
     * Return service from registry.
     *
     * @throws NonExistingServiceException
     */
    public function get(string $serviceId): object;

    /**
     * Remove service from registry.
     *
     * @throws NonExistingServiceException
     */
    public function remove(string $serviceId): void;

    /**
     * Clear all service from regsitry.
     */
    public function clear(): void;

    /**
     * Return key of services from registry.
     */
    public function keys(): array;

    /**
     * Return services from registry.
     */
    public function values(): array;
}
