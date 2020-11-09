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

        $formConfig = $form->getConfig();

        $this->assertSame('code', $formConfig->getOption('choice_value'));
        $this->assertSame('name', $formConfig->getOption('choice_label'));
        $this->assertInstanceOf(ObjectRepository::class, $formConfig->getOption('region_repository'));
    }
}
