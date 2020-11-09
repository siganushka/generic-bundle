<?php

namespace Siganushka\GenericBundle\Tests\Controller;

use Siganushka\GenericBundle\Controller\RegionController;
use Siganushka\GenericBundle\Serializer\Normalizer\RegionNormalizer;
use Siganushka\GenericBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegionControllerTest extends AbstractRegionTest
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $serializer = new Serializer([new RegionNormalizer()]);

        $this->controller = new RegionController($dispatcher, $serializer, $this->managerRegistry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->controller = null;
    }

    public function testInvoke()
    {
        $request = new Request();
        $response = $this->controller->__invoke($request);

        $this->assertSame('[{"code":"100000","name":"foo"}]', $response->getContent());

        $request = new Request(['parent' => '100000']);
        $response = $this->controller->__invoke($request);

        $this->assertSame('[{"code":"200000","name":"bar"}]', $response->getContent());
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
