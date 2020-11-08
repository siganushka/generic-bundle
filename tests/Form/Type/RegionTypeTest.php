<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Symfony\Component\Form\FormFactoryBuilder;

class RegionTypeTest extends TestCase
{
    public function testRegionType()
    {
        $objectRepository = $this->createMock(ObjectRepository::class);

        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $managerRegistry->expects($this->any())
            ->method('getRepository')
            ->willReturn($objectRepository);

        $type = new RegionType($managerRegistry);

        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType($type);

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(RegionType::class)
            ->getForm();

        $options = $form->getConfig()->getOptions();

        $this->assertSame('code', $options['choice_value']);
        $this->assertSame('name', $options['choice_label']);
        $this->assertInstanceOf(ObjectRepository::class, $options['region_repository']);
    }
}
