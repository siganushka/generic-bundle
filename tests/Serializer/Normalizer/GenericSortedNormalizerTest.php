<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Normalizer;

use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\GenericBundle\Serializer\Normalizer\GenericSortedNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GenericSortedNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $foo = new Foo(128);
        $foo->setName('test');

        $objectNormalizer = new ObjectNormalizer();
        $oenericSortedNormalizer = new GenericSortedNormalizer($objectNormalizer);

        static::assertSame([
            'name' => 'test',
            'id' => 128,
        ], $objectNormalizer->normalize($foo));

        static::assertSame([
            'id' => 128,
            'name' => 'test',
        ], $oenericSortedNormalizer->normalize($foo));
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new GenericSortedNormalizer(new ObjectNormalizer());

        static::assertFalse($normalizer->supportsNormalization(new \stdClass()));
        static::assertTrue($normalizer->supportsNormalization(new Foo(128)));
    }
}

class Foo implements ResourceInterface
{
    use ResourceTrait;

    private ?string $name = null;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
