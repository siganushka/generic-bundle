<?php

namespace Siganushka\GenericBundle\Tests\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Form\Type\RegionType;
use Siganushka\GenericBundle\Model\Region;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Component\Form\Test\TypeTestCase;

class RegionTypeTest extends TypeTestCase
{
    private $em;
    private $emRegistry;

    protected function setUp(): void
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        // Compatible doctrine/persistence <=2.0
        if (interface_exists(ManagerRegistry::class)) {
            $emRegistry = $this->createMock(ManagerRegistry::class);
        } else {
            $emRegistry = $this->createMock('\Doctrine\Common\Persistence\ManagerRegistry');
        }

        $emRegistry->expects($this->any())
            ->method('getManager')
            ->with($this->equalTo('default'))
            ->willReturn($em);

        $this->em = $em;
        $this->emRegistry = $emRegistry;

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em = null;
        $this->emRegistry = null;
    }

    public function testRegionType()
    {
        $form = $this->factory->create(RegionType::class, null, [
            'em' => 'default',
        ]);

        $options = $form->getConfig()->getOptions();

        $this->assertSame(Region::class, $options['class']);
        $this->assertSame('name', $options['choice_label']);
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), [
            new DoctrineOrmExtension($this->emRegistry),
        ]);
    }
}
