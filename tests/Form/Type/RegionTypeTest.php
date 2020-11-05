<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Symfony\Component\Form\FormFactoryBuilder;

class RegionTypeTest extends TestCase
{
    public function testRegionType()
    {
        $formFactoryBuilder = new FormFactoryBuilder();

        $form = $formFactoryBuilder->getFormFactory()
            ->createBuilder(RegionType::class)
            ->getForm();

        $options = $form->getConfig()->getOptions();

        $this->assertSame('code', $options['choice_value']);
        $this->assertSame('name', $options['choice_label']);
    }
}
