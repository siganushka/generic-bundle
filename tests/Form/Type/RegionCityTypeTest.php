<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\RegionCityType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class RegionCityTypeTest extends AbstractRegionTypeTest
{
    public function testRegionCityType()
    {
        $form = $this->createFormBuilder(RegionCityType::class)
            ->getForm();

        $this->assertSame([], $form->getConfig()->getOption('choices'));
        $this->assertSame([], $form->getConfig()->getOption('district_options'));
        $this->assertNull($form->getConfig()->getOption('parent'));
    }

    public function testRegionCityTypeWithOptions()
    {
        $options = [
            'parent' => $this->province,
            'district_options' => ['placeholder' => 'baz'],
        ];

        $form = $this->createFormBuilder(RegionCityType::class, null, $options)
            ->getForm();

        $this->assertSame([$this->city], $form->getConfig()->getOption('choices'));
        $this->assertSame($options['district_options'], $form->getConfig()->getOption('district_options'));
        $this->assertSame($this->province, $form->getConfig()->getOption('parent'));

        $this->assertNull($form->getData());
        $this->assertFalse($form->isSubmitted());

        $form->submit('200000');

        $this->assertSame($this->city, $form->getData());
        $this->assertTrue($form->isSubmitted());
    }

    public function testRegionCityTypeParentException()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFormBuilder(RegionCityType::class, null, [
            'parent' => new \stdClass(),
        ]);
    }
}
