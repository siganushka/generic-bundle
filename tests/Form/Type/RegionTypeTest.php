<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Siganushka\GenericBundle\Manager\RegionManagerInterface;
use Symfony\Component\Form\FormFactoryBuilder;

class RegionTypeTest extends TestCase
{
    public function testRegionType()
    {
        $regionManager = $this->createMock(RegionManagerInterface::class);
        $type = new RegionType($regionManager);

        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType($type);

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(RegionType::class)
            ->getForm();

        $options = $form->getConfig()->getOptions();

        $this->assertSame('code', $options['choice_value']);
        $this->assertSame('name', $options['choice_label']);
        $this->assertInstanceOf(RegionManagerInterface::class, $options['region_manager']);
    }
}
