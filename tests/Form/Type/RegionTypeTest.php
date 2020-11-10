<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Doctrine\Persistence\ObjectRepository;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Siganushka\GenericBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\Form\FormFactoryBuilder;

class RegionTypeTest extends AbstractRegionTest
{
    public function testRegionType()
    {
        $type = new RegionType($this->managerRegistry);

        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType($type);

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(RegionType::class)
            ->getForm();

        $this->assertSame('code', $form->getConfig()->getOption('choice_value'));
        $this->assertSame('name', $form->getConfig()->getOption('choice_label'));
        $this->assertInstanceOf(ObjectRepository::class, $form->getConfig()->getOption('region_repository'));
    }
}
