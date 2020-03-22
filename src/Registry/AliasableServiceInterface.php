<?php

namespace Siganushka\GenericBundle\Registry;

interface AliasableServiceInterface
{
    /**
     * Returns alias of services in registry.
     */
    public function getAlias(): string;
}
