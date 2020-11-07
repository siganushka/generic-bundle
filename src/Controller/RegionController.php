<?php

namespace Siganushka\GenericBundle\Controller;

use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Siganushka\GenericBundle\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private $dispatcher;
    private $normalizer;
    private $regionRepository;

    public function __construct(EventDispatcherInterface $dispatcher, NormalizerInterface $normalizer, RegionRepository $regionRepository)
    {
        $this->dispatcher = $dispatcher;
        $this->normalizer = $normalizer;
        $this->regionRepository = $regionRepository;
    }

    public function __invoke(Request $request)
    {
        $regions = $this->getRegions($request);

        $event = new RegionFilterEvent($regions);
        $this->dispatcher->dispatch($event);

        $data = $this->normalizer->normalize($event->getRegions());

        return new JsonResponse($data);
    }

    private function getRegions(Request $request)
    {
        if (!$request->query->has('parent')) {
            return $this->regionRepository->getProvinces();
        }

        $parent = $request->query->get('parent');
        if (!$region = $this->regionRepository->find($parent)) {
            throw new NotFoundHttpException(sprintf('The parent "%s" could not be found.', $parent));
        }

        return $region->getChildren();
    }
}
