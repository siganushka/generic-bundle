<?php

namespace Siganushka\GenericBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Entity\Region;
use Siganushka\GenericBundle\Event\RegionFilterEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionController
{
    private $dispatcher;
    private $normalizer;
    private $repository;

    public function __construct(EventDispatcherInterface $dispatcher, NormalizerInterface $normalizer, ManagerRegistry $managerRegistry)
    {
        $this->dispatcher = $dispatcher;
        $this->normalizer = $normalizer;
        $this->repository = $managerRegistry->getRepository(Region::class);
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
            return $this->repository->findBy(['parent' => null], ['parent' => 'ASC', 'id' => 'ASC']);
        }

        $parent = $request->query->get('parent');
        if (!$region = $this->repository->find($parent)) {
            throw new NotFoundHttpException(sprintf('The parent "%s" could not be found.', $parent));
        }

        return $region->getChildren();
    }
}
