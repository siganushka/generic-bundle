<?php

namespace Siganushka\GenericBundle\Tests\Controller;

use Doctrine\ORM\AbstractQuery;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Controller\RegionController;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionControllerTest extends TestCase
{
    public function testInvoke()
    {
        $region = new Region();
        $region->setCode(1);
        $region->setName('foo');

        $query = $this->createMock(AbstractQuery::class);

        $query->expects($this->any())
            ->method('getResult')
            ->willReturn([$region]);

        $repository = $this->createMock(RegionRepository::class);

        $repository->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);

        $regionAsArray = (new GetSetMethodNormalizer())->normalize($region, null, [
            AbstractNormalizer::ATTRIBUTES => ['code', 'name'],
        ]);

        $normalizer->expects($this->any())
            ->method('normalize')
            ->willReturn([$regionAsArray]);

        $controller = new RegionController($eventDispatcher, $normalizer);

        $request = new Request();
        $response = $controller->__invoke($request, $repository);

        $this->assertStringContainsString('code', $response->getContent());
        $this->assertStringContainsString('name', $response->getContent());

        $data = json_decode($response->getContent(), true);

        $this->assertSame([$regionAsArray], $data);
    }
}
