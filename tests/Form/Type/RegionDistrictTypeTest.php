<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\RegionDistrictType;

class RegionDistrictTypeTest extends AbstractRegionTypeTest
{
    public function testRegionDistrictType()
    {
        $form = $this->createFormBuilder(RegionDistrictType::class)
            ->getForm();

        $this->assertNull($form->getConfig()->getOption('parent'));
    }

    public function testRegionDistrictTypeWithOptions()
    {
        $options = ['parent' => $this->city];

        $form = $this->createFormBuilder(RegionDistrictType::class, null, $options)
            ->getForm();

        $this->assertSame($options['parent'], $form->getConfig()->getOption('parent'));
    }
}
