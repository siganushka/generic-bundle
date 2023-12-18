<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class KnpPaginationNormalizer implements NormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    use SerializerAwareTrait;

    public const CURRENT_PAGE_NUMBER_KEY = 'knp_current_page_number';
    public const ITEMS_PER_PAGE_KEY = 'knp_items_per_page';
    public const TOTAL_COUNT_KEY = 'knp_total_count';
    public const ITEMS_KEY = 'knp_items';

    private array $defaultContext = [
        self::CURRENT_PAGE_NUMBER_KEY => 'currentPageNumber',
        self::ITEMS_PER_PAGE_KEY => 'itemsPerPage',
        self::TOTAL_COUNT_KEY => 'totalCount',
        self::ITEMS_KEY => 'items',
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param PaginationInterface|mixed $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException('Cannot normalize because the injected serializer is not a normalizer.');
        }

        $items = [];
        foreach ($object->getItems() as $item) {
            $items[] = \is_object($item) ? $this->serializer->normalize($item, $format, $context) : $item;
        }

        return [
            $context[self::CURRENT_PAGE_NUMBER_KEY] ?? $this->defaultContext[self::CURRENT_PAGE_NUMBER_KEY] => $object->getCurrentPageNumber(),
            $context[self::ITEMS_PER_PAGE_KEY] ?? $this->defaultContext[self::ITEMS_PER_PAGE_KEY] => $object->getItemNumberPerPage(),
            $context[self::TOTAL_COUNT_KEY] ?? $this->defaultContext[self::TOTAL_COUNT_KEY] => $object->getTotalItemCount(),
            $context[self::ITEMS_KEY] ?? $this->defaultContext[self::ITEMS_KEY] => $items,
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof PaginationInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
