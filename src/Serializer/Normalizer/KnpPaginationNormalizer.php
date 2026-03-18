<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class KnpPaginationNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public const TOTAL_COUNT_KEY = 'knp_total_count';
    public const CURRENT_PAGE_KEY = 'knp_current_page';
    public const ITEMS_PER_PAGE_KEY = 'knp_items_per_page';
    public const ITEMS_KEY = 'knp_items';

    private array $defaultContext = [
        self::TOTAL_COUNT_KEY => 'totalCount',
        self::CURRENT_PAGE_KEY => 'currentPage',
        self::ITEMS_PER_PAGE_KEY => 'itemsPerPage',
        self::ITEMS_KEY => 'items',
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param PaginationInterface<mixed, mixed> $object
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return [
            $context[self::TOTAL_COUNT_KEY] ?? $this->defaultContext[self::TOTAL_COUNT_KEY] => $object->getTotalItemCount(),
            $context[self::CURRENT_PAGE_KEY] ?? $this->defaultContext[self::CURRENT_PAGE_KEY] => $object->getCurrentPageNumber(),
            $context[self::ITEMS_PER_PAGE_KEY] ?? $this->defaultContext[self::ITEMS_PER_PAGE_KEY] => $object->getItemNumberPerPage(),
            $context[self::ITEMS_KEY] ?? $this->defaultContext[self::ITEMS_KEY] => $this->normalizer->normalize($object->getItems(), $format, $context),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PaginationInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginationInterface::class => true,
        ];
    }
}
