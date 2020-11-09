<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Siganushka\GenericBundle\Form\Type\RegionType;
use Siganushka\GenericBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\Form\FormFactoryBuilder;

abstract class AbstractRegionTypeTest extends AbstractRegionTest
{
    protected function createFormBuilder(string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = [])
    {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType(new RegionType($this->managerRegistry));

        $formBuilder = $formFactoryBuilder->getFormFactory()
            ->createBuilder($type, $data, $options);

        return $formBuilder;
    }
}
