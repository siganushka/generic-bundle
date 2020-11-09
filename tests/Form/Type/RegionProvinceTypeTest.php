<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Doctrine\Persistence\ObjectRepository;
use Siganushka\GenericBundle\Form\Type\RegionProvinceType;

class RegionProvinceTypeTest extends AbstractRegionTypeTest
{
    public function testRegionProvinceType()
    {
        $options = [
            'placeholder' => 'foo',
            'city_options' => ['placeholder' => 'bar'],
            'district_options' => ['placeholder' => 'baz'],
        ];

        $form = $this->createFormBuilder()
            ->add('province', RegionProvinceType::class, $options)
            ->getForm();

        $this->assertTrue($form->has('province'));
        $this->assertTrue($form->has('city'));
        $this->assertTrue($form->has('district'));

        $this->assertSame($options['placeholder'], $form['province']->getConfig()->getOption('placeholder'));
        $this->assertSame($options['city_options']['placeholder'], $form['city']->getConfig()->getOption('placeholder'));
        $this->assertSame($options['district_options']['placeholder'], $form['district']->getConfig()->getOption('placeholder'));

        $this->assertInstanceOf(ObjectRepository::class, $form['province']->getConfig()->getOption('region_repository'));
        $this->assertInstanceOf(ObjectRepository::class, $form['city']->getConfig()->getOption('region_repository'));
        $this->assertInstanceOf(ObjectRepository::class, $form['district']->getConfig()->getOption('region_repository'));
    }
}
