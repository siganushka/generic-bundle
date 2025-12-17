<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests;

use Knp\Component\Pager\ArgumentAccess\RequestArgumentAccess;
use Knp\Component\Pager\Event\Subscriber\Paginate\ArraySubscriber;
use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Paginator;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Decorator\DecoratingKnpPaginator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DecoratingKnpPaginatorTest extends TestCase
{
    public function testAll(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new ArraySubscriber());
        $eventDispatcher->addSubscriber(new PaginationSubscriber());

        $requestStack1 = $this->createMock(RequestStack::class);
        $requestStack2 = $this->createMock(RequestStack::class);
        $requestStack2->expects(static::any())
            ->method('getCurrentRequest')
            ->willReturn(Request::create('/', parameters: ['page' => 2, 'limit' => 20]))
        ;

        $requestStack3 = $this->createMock(RequestStack::class);
        $requestStack3->expects(static::any())
            ->method('getCurrentRequest')
            ->willReturn(Request::create('/', parameters: ['page' => 3, 'size' => 30]))
        ;

        $decorated = new Paginator($eventDispatcher, new RequestArgumentAccess($requestStack1));

        $pagination = $decorated->paginate([]);
        static::assertSame(1, $pagination->getCurrentPageNumber());
        static::assertSame(10, $pagination->getItemNumberPerPage());

        $paginator = new DecoratingKnpPaginator($decorated, $requestStack1);

        $pagination = $paginator->paginate([]);
        static::assertSame(1, $pagination->getCurrentPageNumber());
        static::assertSame(10, $pagination->getItemNumberPerPage());

        $pagination = $paginator->paginate([], 2, 5);
        static::assertSame(2, $pagination->getCurrentPageNumber());
        static::assertSame(5, $pagination->getItemNumberPerPage());

        $paginator = new DecoratingKnpPaginator($decorated, $requestStack2);

        $pagination = $paginator->paginate([]);
        static::assertSame(2, $pagination->getCurrentPageNumber());
        static::assertSame(20, $pagination->getItemNumberPerPage());

        $pagination = $paginator->paginate([], options: [DecoratingKnpPaginator::LIMIT_THRESHOLD => 15]);
        static::assertSame(2, $pagination->getCurrentPageNumber());
        static::assertSame(15, $pagination->getItemNumberPerPage());

        $paginator = new DecoratingKnpPaginator($decorated, $requestStack3);

        $pagination = $paginator->paginate([]);
        static::assertSame(3, $pagination->getCurrentPageNumber());
        static::assertSame(30, $pagination->getItemNumberPerPage());
    }
}
