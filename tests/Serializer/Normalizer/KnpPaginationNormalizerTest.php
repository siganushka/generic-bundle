<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Serializer\Normalizer;

use Knp\Component\Pager\Pagination\SlidingPagination;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Serializer\Normalizer\KnpPaginationNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class KnpPaginationNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $context = [
            KnpPaginationNormalizer::TOTAL_COUNT_KEY => 'total',
            KnpPaginationNormalizer::CURRENT_PAGE_KEY => 'page',
            KnpPaginationNormalizer::ITEMS_PER_PAGE_KEY => 'per_page',
            KnpPaginationNormalizer::ITEMS_KEY => 'data',
        ];

        $pagination = new SlidingPagination();
        $pagination->setCurrentPageNumber(2);
        $pagination->setItemNumberPerPage(10);
        $pagination->setTotalItemCount(32);
        $pagination->setItems([1, 2, 3, 4]);

        $normalizer = $this->createNormalizerWithContext();
        $normalizerWithContext = $this->createNormalizerWithContext($context);

        static::assertSame([
            'totalCount' => $pagination->getTotalItemCount(),
            'currentPage' => $pagination->getCurrentPageNumber(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'items' => $pagination->getItems(),
        ], $normalizer->normalize($pagination));

        static::assertSame([
            'total' => $pagination->getTotalItemCount(),
            'page' => $pagination->getCurrentPageNumber(),
            'per_page' => $pagination->getItemNumberPerPage(),
            'data' => $pagination->getItems(),
        ], $normalizer->normalize($pagination, null, $context));

        static::assertSame([
            'total' => $pagination->getTotalItemCount(),
            'page' => $pagination->getCurrentPageNumber(),
            'per_page' => $pagination->getItemNumberPerPage(),
            'data' => $pagination->getItems(),
        ], $normalizerWithContext->normalize($pagination));
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = $this->createNormalizerWithContext();

        static::assertFalse($normalizer->supportsNormalization(new \stdClass()));
        static::assertTrue($normalizer->supportsNormalization(new SlidingPagination()));
    }

    protected function createNormalizerWithContext(array $context = []): NormalizerInterface
    {
        $innerNormalizer = $this->createMock(NormalizerInterface::class);

        $innerNormalizer->expects(static::any())
            ->method('normalize')
            ->willReturnCallback(static fn (mixed $items) => $items)
        ;

        $normalizer = new KnpPaginationNormalizer($context);
        $normalizer->setNormalizer($innerNormalizer);

        return $normalizer;
    }
}
