<?php

namespace Siganushka\GenericBundle\Tests\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Controller\RegionController;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Serializer\Normalizer\RegionNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionControllerTest extends TestCase
{
    private $controller;
    private $province;

    protected function setUp(): void
    {
        $city = new Region();
        $city->setCode('100100');
        $city->setName('bar');

        $province = new Region();
        $province->setCode('100000');
        $province->setName('foo');
        $province->addChild($city);

        $objectRepository = $this->createMock(ObjectRepository::class);

        $objectRepository->expects($this->any())
            ->method('findBy')
            ->willReturn([$province]);

        $objectRepository->expects($this->any())
            ->method('find')
            ->willReturnCallback(function ($value) use ($province) {
                return ('100000' == $value) ? $province : null;
            });

        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $managerRegistry->expects($this->any())
            ->method('getRepository')
            ->willReturn($objectRepository);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $serializer = new Serializer([new RegionNormalizer()]);

        $this->controller = new RegionController($dispatcher, $serializer, $managerRegistry);
        $this->province = $province;
    }

    protected function tearDown(): void
    {
        $this->controller = null;
        $this->province = null;
    }

    public function testInvoke()
    {
        $request = new Request();
        $response = $this->controller->__invoke($request);

        $this->assertSame('[{"code":"100000","name":"foo"}]', $response->getContent());

        $request = new Request(['parent' => '100000']);
        $response = $this->controller->__invoke($request);

        $this->assertSame('[{"code":"100100","name":"bar"}]', $response->getContent());
    }

    public function testGetRegions()
    {
        $method = new \ReflectionMethod($this->controller, 'getRegions');
        $method->setAccessible(true);

        $request = new Request();
        $regions = $method->invokeArgs($this->controller, [$request]);

        $this->assertSame([$this->province], $regions);

        $request = new Request(['parent' => '100000']);
        $regions = $method->invokeArgs($this->controller, [$request]);

        $this->assertSame($this->province->getChildren(), $regions);
    }

    public function testGetRegionsException()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The parent "123" could not be found.');

        $method = new \ReflectionMethod($this->controller, 'getRegions');
        $method->setAccessible(true);

        $request = new Request(['parent' => '123']);
        $method->invokeArgs($this->controller, [$request]);
    }
}
