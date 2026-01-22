<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\Filter\DeletableFilter;

class DeletableFilterTest extends TestCase
{
    public function testAll(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $reflClass = $this->createMock(\ReflectionClass::class);
        $reflClass->expects(static::any())
            ->method('implementsInterface')
            ->willReturn(true)
        ;

        $foo = $this->createMock(ClassMetadata::class);
        $bar = $this->createMock(ClassMetadata::class);
        $bar->reflClass = $reflClass;

        $filter = new DeletableFilter($entityManager);
        static::assertSame('', $filter->addFilterConstraint($foo, 'foo'));
        static::assertSame('bar.deleted = 0', $filter->addFilterConstraint($bar, 'bar'));
    }
}
