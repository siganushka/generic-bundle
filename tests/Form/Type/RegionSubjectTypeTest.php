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

        $this->assertSame([], $form['foo']->getConfig()->getOption('province_options'));
        $this->assertSame([], $form['foo']->getConfig()->getOption('city_options'));
        $this->assertSame([], $form['foo']->getConfig()->getOption('district_options'));

        $this->assertSame([$this->province], $form['foo']['province']->getConfig()->getOption('choices'));
        $this->assertSame([], $form['foo']['city']->getConfig()->getOption('choices'));
        $this->assertSame([], $form['foo']['district']->getConfig()->getOption('choices'));

        $this->assertTrue($form['foo']->getConfig()->getOption('inherit_data'));

        $this->assertNull($form->getData());
        $this->assertFalse($form->isSubmitted());

        $form->submit(['foo' => ['province' => '100000', 'city' => '200000', 'district' => '300000']]);

        $data = [
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
        ];

        $this->assertSame([$data['province']], $form['foo']['province']->getConfig()->getOption('choices'));
        $this->assertSame([$data['city']], $form['foo']['city']->getConfig()->getOption('choices'));
        $this->assertSame([$data['district']], $form['foo']['district']->getConfig()->getOption('choices'));

        $this->assertSame($data, $form->getData());
        $this->assertTrue($form->isSubmitted());
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
