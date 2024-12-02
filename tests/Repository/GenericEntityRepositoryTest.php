<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\SortableInterface;
use Siganushka\Contracts\Doctrine\SortableTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;

class GenericEntityRepositoryTest extends TestCase
{
    public function testAll(): void
    {
        $repository = $this->createRepository(Foo::class);
        static::assertInstanceOf(GenericEntityRepository::class, $repository);

        /** @var Foo */
        $entity = $repository->createNew('foo');
        static::assertInstanceOf(Foo::class, $entity);
        static::assertSame('foo', $entity->getArg1());
        static::assertSame(128, $entity->getArg2());

        /** @var Foo */
        $entity = $repository->createNew('bar', 256);
        static::assertInstanceOf(Foo::class, $entity);
        static::assertSame('bar', $entity->getArg1());
        static::assertSame(256, $entity->getArg2());

        $entity->setArg1('baz');
        $entity->setArg2(512);
        static::assertSame('baz', $entity->getArg1());
        static::assertSame(512, $entity->getArg2());

        static::assertSame(
            'SELECT f FROM Siganushka\GenericBundle\Tests\Repository\Foo f ORDER BY f.sort DESC, f.createdAt DESC, f.id DESC',
            $repository->createQueryBuilderWithOrdered('f')->getDQL()
        );
    }

    public function testArgumentCountError(): void
    {
        $this->expectException(\ArgumentCountError::class);

        $repository = $this->createRepository(Foo::class);
        $repository->createNew();
    }

    /**
     * @phpstan-param class-string $entityClass
     *
     * @return GenericEntityRepository<object>
     */
    private function createRepository(string $entityClass): GenericEntityRepository
    {
        $classMetadata = new ClassMetadata($entityClass);

        /** @var MockObject&EntityManagerInterface */
        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $entityManager->expects(static::any())
            ->method('createQueryBuilder')
            ->willReturn(new QueryBuilder($entityManager))
        ;

        /** @var MockObject&ManagerRegistry */
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects(static::any())
            ->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        return new GenericEntityRepository($managerRegistry, $entityClass);
    }
}

class Foo implements ResourceInterface, SortableInterface, TimestampableInterface
{
    use ResourceTrait;
    use SortableTrait;
    use TimestampableTrait;

    private ?string $arg1 = null;
    private ?int $arg2 = null;

    public function __construct(string $arg1, int $arg2 = 128)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function setArg1(string $arg1): self
    {
        $this->arg1 = $arg1;

        return $this;
    }

    public function getArg1(): ?string
    {
        return $this->arg1;
    }

    public function setArg2(int $arg2): self
    {
        $this->arg2 = $arg2;

        return $this;
    }

    public function getArg2(): ?int
    {
        return $this->arg2;
    }
}
