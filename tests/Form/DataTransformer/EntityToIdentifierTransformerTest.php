<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Form\DataTransformer;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\DataTransformer\EntityToIdentifierTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdentifierTransformerTest extends TestCase
{
    protected Foo $foo;

    protected function setUp(): void
    {
        $this->foo = new Foo();
        $this->foo->id = 128;
    }

    public function testTransform(): void
    {
        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');

        static::assertEquals($this->foo->id, $transformer->transform($this->foo));
    }

    public function testTransformNullValue(): void
    {
        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        static::assertNull($transformer->reverseTransform(null));
    }

    public function testTransformEmptyValue(): void
    {
        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        static::assertNull($transformer->reverseTransform(''));
    }

    public function testReverseTransformNullValue(): void
    {
        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        static::assertNull($transformer->transform(null));
    }

    public function testTransformInvalidValueException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        $transformer->transform(new \stdClass());
    }

    public function testTransformInvalidIdentifierFieldException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'non_existing_field');
        $transformer->transform($this->foo);
    }

    public function testReverseTransform(): void
    {
        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');

        static::assertNull($transformer->reverseTransform(null));
        static::assertNull($transformer->reverseTransform(''));

        static::assertEquals($this->foo, $transformer->reverseTransform(128));
    }

    public function testReverseTransformInvalidValueException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        $transformer->reverseTransform($this->foo);
    }

    public function testReverseTransformInvalidIdentifierFieldException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'non_existing_field');
        $transformer->reverseTransform(128);
    }

    public function testReverseTransformNotFoundException(): void
    {
        $this->expectException(TransformationFailedException::class);

        $transformer = $this->createEntityToIdentifierTransformer(Foo::class, 'id');
        $transformer->reverseTransform(0);
    }

    /**
     * @param class-string $className
     */
    private function createEntityToIdentifierTransformer(string $className, string $identifierField): EntityToIdentifierTransformer
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects(static::any())
            ->method('hasField')
            ->willReturnCallback(fn (string $value) => ('id' === $value) ? true : false)
        ;

        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->expects(static::any())
            ->method('findOneBy')
            ->willReturnCallback(fn (array $value) => $value === ['id' => 128] ? $this->foo : null)
        ;

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $objectManager->expects(static::any())
            ->method('getRepository')
            ->willReturn($objectRepository)
        ;

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->expects(static::any())
            ->method('getManagerForClass')
            ->willReturn($objectManager)
        ;

        return new EntityToIdentifierTransformer($managerRegistry, $className, $identifierField);
    }
}

class Foo
{
    public ?int $id = null;
}
