<?php

namespace Siganushka\GenericBundle\Registry;

interface RegistryInterface
{
    public function register(object $service): void;

    public function has(string $serviceId): bool;

    public function get(string $serviceId): object;

    public function remove(string $serviceId): void;

    public function clear(): void;

    public function keys(): array;

    public function values(): array;
}
