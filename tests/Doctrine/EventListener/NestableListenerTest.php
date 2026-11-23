<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Doctrine\EventListener\NestableListener;
use Siganushka\GenericBundle\Entity\AbstractNestable;
use Siganushka\GenericBundle\Tests\Fixtures\TestNested;

class NestableListenerTest extends TestCase
{
    public function testMapParentAndChildrenAssociations(): void
    {
        $metadata = new ClassMetadata(TestNested::class);
        $metadata->setIdentifier(['id']);

        (new NestableListener())->loadClassMetadata($this->createEvent($metadata));

        static::assertArrayHasKey('parent', $metadata->associationMappings);
        static::assertArrayHasKey('children', $metadata->associationMappings);
    }

    public function testIgnoreMappedSuperclass(): void
    {
        $metadata = new ClassMetadata(AbstractNestable::class);
        $metadata->isMappedSuperclass = true;

        (new NestableListener())->loadClassMetadata($this->createEvent($metadata));

        static::assertSame([], $metadata->associationMappings);
    }

    public function testIgnoreNonNestableEntity(): void
    {
        $metadata = new ClassMetadata(\stdClass::class);

        (new NestableListener())->loadClassMetadata($this->createEvent($metadata));

        static::assertSame([], $metadata->associationMappings);
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function createEvent(ClassMetadata $metadata): LoadClassMetadataEventArgs
    {
        return new LoadClassMetadataEventArgs($metadata, $this->createMock(EntityManagerInterface::class));
    }
}
