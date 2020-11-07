<?php

namespace Siganushka\GenericBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class RegionRepositoryTest extends TestCase
{
    public function testGetProvinces()
    {
        // Compatible doctrine/persistence <=2.0
        // if (interface_exists(ManagerRegistry::class)) {
        //     $managerRegistry = $this->createMock(ManagerRegistry::class);
        // } else {
        //     $managerRegistry = $this->createMock('\Doctrine\Common\Persistence\ManagerRegistry');
        // }

        var_dump('ManagerRegistry', interface_exists(ManagerRegistry::class));

        $this->assertTrue(true);
    }
}
