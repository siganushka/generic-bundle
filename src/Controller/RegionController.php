<?php

namespace Siganushka\GenericBundle\Controller;

use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private $dispatcher;
    private $normalizer;

    public function __construct(EventDispatcherInterface $dispatcher, NormalizerInterface $normalizer)
    {
        $this->dispatcher = $dispatcher;
        $this->normalizer = $normalizer;
    }

    public function __invoke(Request $request, RegionRepository $repository)
    {
        $parent = $request->query->get('parent', null);

        $query = $repository->getQuery($parent);
        $regions = $query->getResult();

        $event = new RegionFilterEvent($regions);
        $this->dispatcher->dispatch($event);

        $data = $this->normalizer->normalize($event->getRegions(), null, [
            AbstractNormalizer::ATTRIBUTES => ['code', 'name'],
        ]);

        return new JsonResponse($data);
    }
}
