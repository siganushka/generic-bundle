<?php

namespace Siganushka\GenericBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Command\RegionUpdateCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegionUpdateCommandTest extends TestCase
{
    public function testExecute()
    {
        $this->expectException(\UnexpectedValueException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $command = new RegionUpdateCommand($httpClient, $entityManager);

        $application = new Application($this->getKernel());
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => 'siganushka:region:update']);
    }

    private function getKernel(): object
    {
        $container = $this
            ->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->getMock();

        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->willReturn($container);

        $kernel
            ->expects($this->once())
            ->method('getBundles')
            ->willReturn([]);

        return $kernel;
    }
}
