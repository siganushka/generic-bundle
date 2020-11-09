<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\RegionSubjectType;

class RegionSubjectTypeTest extends AbstractRegionTypeTest
{
    public function testRegionSubjectType()
    {
        $form = $this->createFormBuilder()
            ->add('foo', RegionSubjectType::class)
            ->getForm();

        $this->assertTrue($form['foo']->has('province'));
        $this->assertTrue($form['foo']->has('city'));
        $this->assertTrue($form['foo']->has('district'));

        $formConfig = $form['foo']->getConfig();

        $this->assertTrue($formConfig->getOption('inherit_data'));
        $this->assertSame([], $formConfig->getOption('province_options'));
        $this->assertSame([], $formConfig->getOption('city_options'));
        $this->assertSame([], $formConfig->getOption('district_options'));
    }

    public function testRegionSubjectTypeByRoot()
    {
        $form = $this->createFormBuilder(RegionSubjectType::class)
            ->getForm();

        $this->assertTrue($form->has('province'));
        $this->assertFalse($form->has('city'));
        $this->assertFalse($form->has('district'));
    }
}
