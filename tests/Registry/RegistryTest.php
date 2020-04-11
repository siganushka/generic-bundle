<?php

namespace Siganushka\GenericBundle\Tests\Registry;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Exception\AbstractionNotFoundException;
use Siganushka\GenericBundle\Exception\ExistingServiceException;
use Siganushka\GenericBundle\Exception\NonExistingServiceException;
use Siganushka\GenericBundle\Exception\UnsupportedServiceException;
use Siganushka\GenericBundle\Registry\AbstractRegistry;
use Siganushka\GenericBundle\Registry\AliasableServiceInterface;

class RegistryTest extends TestCase
{
    public function testAll()
    {
        $foo = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'FooService');
        $bar = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'BarService');

        $aliasableBaz = $this->getMockForAbstractClass(AliasableRegistrySubjectInterface::class, [], 'AliasableBazService');
        $aliasableBaz->expects($this->any())
            ->method('getAlias')
            ->willReturn('baz');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->register($foo);
        $registry->register($bar);
        $registry->register($aliasableBaz);

        $this->assertCount(3, $registry->values());
        $this->assertEquals(['FooService', 'BarService', 'baz'], $registry->keys());

        $this->assertTrue($registry->has('FooService'));
        $this->assertTrue($registry->has('BarService'));
        $this->assertTrue($registry->has('baz'));

        $this->assertEquals($foo, $registry->get('FooService'));
        $this->assertEquals($bar, $registry->get('BarService'));
        $this->assertEquals($aliasableBaz, $registry->get('baz'));

        $registry->remove('FooService');

        $this->assertCount(2, $registry->values());
        $this->assertEquals(['BarService', 'baz'], $registry->keys());

        $registry->clear();

        $this->assertCount(0, $registry->values());
        $this->assertCount(0, $registry->keys());
    }

    public function testAbstractionNotFoundException()
    {
        $this->expectException(AbstractionNotFoundException::class);
        $this->expectExceptionMessage('Abstraction NotFoundInterface for ServiceRegistry could not be found.');

        $this->getMockForAbstractClass(AbstractRegistry::class, ['NotFoundInterface'], 'ServiceRegistry');
    }

    public function testRegisterUnsupportedServiceException()
    {
        $this->expectException(UnsupportedServiceException::class);
        $this->expectExceptionMessage('Service stdClass for registry ServiceRegistry is unsupported.');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->register(new \stdClass());
    }

    public function testRegisterExistingServiceException()
    {
        $this->expectException(ExistingServiceException::class);
        $this->expectExceptionMessage('Service FooService for registry ServiceRegistry already exists.');

        $foo = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'FooService');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->register($foo);
        $registry->register($foo);
    }

    public function testRegisterNonExistingException()
    {
        $this->expectException(NonExistingServiceException::class);
        $this->expectExceptionMessage('Service NotFoundService for registry ServiceRegistry does not exist.');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->get('NotFoundService');
    }
}

interface RegistrySubjectInterface
{
}

interface AliasableRegistrySubjectInterface extends RegistrySubjectInterface, AliasableServiceInterface
{
}
