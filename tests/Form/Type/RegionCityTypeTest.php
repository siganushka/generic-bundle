<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\RegionCityType;

class RegionCityTypeTest extends AbstractRegionTypeTest
{
    public function testRegionCityType()
    {
        $form = $this->createFormBuilder(RegionCityType::class)
            ->getForm();

        $this->assertNull($form->getConfig()->getOption('parent'));
        $this->assertSame([], $form->getConfig()->getOption('district_options'));
    }

    public function testRegionCityTypeWithOptions()
    {
        $options = ['parent' => $this->province];

        $form = $this->createFormBuilder(RegionCityType::class, null, $options)
            ->getForm();

        $this->assertSame($options['parent'], $form->getConfig()->getOption('parent'));
        $this->assertSame([], $form->getConfig()->getOption('district_options'));
    }
}
