<?php

namespace Siganushka\GenericBundle\Tests\Controller;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Controller\RegionController;
use Siganushka\GenericBundle\Model\Region;
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

        $queryBuilder = $this->createMock(QueryBuilder::class);

        $queryBuilder->expects($this->any())
            ->method('where')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->any())
            ->method('addOrderBy')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $regionRepository = $this->createMock(EntityRepository::class);

        $regionRepository->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($regionRepository);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $normalizer = $this->createMock(NormalizerInterface::class);

        $regionAsArray = (new GetSetMethodNormalizer())->normalize($region, null, [
            AbstractNormalizer::ATTRIBUTES => ['code', 'name'],
        ]);

        $normalizer->expects($this->any())
            ->method('normalize')
            ->willReturn([$regionAsArray]);

        $controller = new RegionController($entityManager, $eventDispatcher, $normalizer);

        $request = new Request();
        $response = $controller->__invoke($request);

        $data = json_decode($response->getContent(), true);

        $this->assertSame([$regionAsArray], $data);
    }
}
