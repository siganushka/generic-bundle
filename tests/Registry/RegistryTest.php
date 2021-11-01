<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Registry;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Registry\AbstractRegistry;
use Siganushka\GenericBundle\Registry\AliasableInterface;
use Siganushka\GenericBundle\Registry\Exception\AbstractionNotFoundException;
use Siganushka\GenericBundle\Registry\Exception\ServiceExistingException;
use Siganushka\GenericBundle\Registry\Exception\ServiceNonExistingException;
use Siganushka\GenericBundle\Registry\Exception\ServiceUnsupportedException;
use Siganushka\GenericBundle\Registry\RegistryInterface;

/**
 * @internal
 * @coversNothing
 */
final class RegistryTest extends TestCase
{
    public function testAll(): void
    {
        $foo = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'FooService');
        $bar = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'BarService');

        $aliasableBaz = $this->getMockForAbstractClass(AliasableRegistrySubjectInterface::class, [], 'AliasableBazService');
        $aliasableBaz->expects(static::any())
            ->method('getAlias')
            ->willReturn('baz')
        ;

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $ret1 = $registry->register($foo);
        $ret2 = $registry->register($bar);
        $ret3 = $registry->register($aliasableBaz);

        static::assertInstanceOf(RegistryInterface::class, $ret1);
        static::assertInstanceOf(RegistryInterface::class, $ret2);
        static::assertInstanceOf(RegistryInterface::class, $ret3);

        static::assertCount(3, $registry->getValues());
        static::assertSame(['FooService', 'BarService', 'baz'], $registry->getKeys());

        static::assertTrue($registry->has('FooService'));
        static::assertTrue($registry->has('BarService'));
        static::assertTrue($registry->has('baz'));

        static::assertSame($foo, $registry->get('FooService'));
        static::assertSame($bar, $registry->get('BarService'));
        static::assertSame($aliasableBaz, $registry->get('baz'));

        $registry->remove('FooService');

        static::assertCount(2, $registry->getValues());
        static::assertSame(['BarService', 'baz'], $registry->getKeys());

        $registry->clear();

        static::assertCount(0, $registry->getValues());
        static::assertCount(0, $registry->getKeys());
    }

    public function testAbstractionNotFoundException(): void
    {
        $this->expectException(AbstractionNotFoundException::class);
        $this->expectExceptionMessage('Abstraction NotFoundInterface for ServiceRegistry could not be found.');

        $this->getMockForAbstractClass(AbstractRegistry::class, ['NotFoundInterface'], 'ServiceRegistry');
    }

    public function testRegisterServiceUnsupportedException(): void
    {
        $this->expectException(ServiceUnsupportedException::class);
        $this->expectExceptionMessage('Service stdClass for registry ServiceRegistry is unsupported.');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->register(new \stdClass());
    }

    public function testRegisterServiceExistingException(): void
    {
        $this->expectException(ServiceExistingException::class);
        $this->expectExceptionMessage('Service FooService for registry ServiceRegistry already exists.');

        $foo = $this->getMockForAbstractClass(RegistrySubjectInterface::class, [], 'FooService');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->register($foo);
        $registry->register($foo);
    }

    public function testRegisterNonExistingException(): void
    {
        $this->expectException(ServiceNonExistingException::class);
        $this->expectExceptionMessage('Service NotFoundService for registry ServiceRegistry does not exist.');

        $registry = $this->getMockForAbstractClass(AbstractRegistry::class, [RegistrySubjectInterface::class], 'ServiceRegistry');
        $registry->get('NotFoundService');
    }
}

interface RegistrySubjectInterface
{
}

interface AliasableRegistrySubjectInterface extends RegistrySubjectInterface, AliasableInterface
{
}
