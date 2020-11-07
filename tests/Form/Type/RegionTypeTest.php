<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Siganushka\GenericBundle\Repository\RegionRepository;
use Symfony\Component\Form\FormFactoryBuilder;

class RegionTypeTest extends TestCase
{
    public function testRegionType()
    {
        $regionRepository = $this->createMock(RegionRepository::class);
        $type = new RegionType($regionRepository);

        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType($type);

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(RegionType::class)
            ->getForm();

        $options = $form->getConfig()->getOptions();

        $this->assertSame('code', $options['choice_value']);
        $this->assertSame('name', $options['choice_label']);
        $this->assertInstanceOf(RegionRepository::class, $options['region_repository']);
    }
}
