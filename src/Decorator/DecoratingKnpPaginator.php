<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Decorator;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DecoratingKnpPaginator implements PaginatorInterface
{
    public const LIMIT_THRESHOLD = 'limit_threshold';
    public const LIMIT_THRESHOLD_VALUE = 100;

    public function __construct(
        private readonly PaginatorInterface $decorated,
        private readonly RequestStack $requestStack,
        private readonly string $pageName = 'page',
        private readonly string $limitName = 'limit',
    ) {
    }

    public function paginate(mixed $target, ?int $page = null, ?int $limit = null, array $options = []): PaginationInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request?->query->has($this->pageName)) {
            $page ??= $request->query->getInt($this->pageName);
        }

        if ($request?->query->has($this->limitName)) {
            $limit ??= $request->query->getInt($this->limitName);
        }

        // Use "limit" instead of "size", but maintain compatibility for now.
        if ($request?->query->has('size')) {
            $limit ??= $request->query->getInt('size');
        }

        /** @var int */
        $limitThreshold = $options[self::LIMIT_THRESHOLD] ?? self::LIMIT_THRESHOLD_VALUE;
        if ($limit && $limit > $limitThreshold) {
            $limit = $limitThreshold;
        }

        return $this->decorated->paginate($target, $page ?? 1, $limit, $options);
    }
}
