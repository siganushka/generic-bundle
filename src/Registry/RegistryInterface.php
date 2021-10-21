<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Registry;

use Siganushka\GenericBundle\Exception\ServiceExistingException;
use Siganushka\GenericBundle\Exception\ServiceNonExistingException;
use Siganushka\GenericBundle\Exception\ServiceUnsupportedException;

interface RegistryInterface
{
    /**
     * Register service for registry.
     *
     * @throws ServiceUnsupportedException
     * @throws ServiceExistingException
     */
    public function register(object $service): self;

    /**
     * Check service if exists.
     */
    public function has(string $serviceId): bool;

    /**
     * Return service from registry.
     *
     * @throws ServiceNonExistingException
     */
    public function get(string $serviceId): object;

    /**
     * Remove service from registry.
     *
     * @throws ServiceNonExistingException
     */
    public function remove(string $serviceId): void;

    /**
     * Clear all service from regsitry.
     */
    public function clear(): void;

    /**
     * Return key of services from registry.
     */
    public function getKeys(): array;

    /**
     * Return services from registry.
     */
    public function getValues(): array;
}
