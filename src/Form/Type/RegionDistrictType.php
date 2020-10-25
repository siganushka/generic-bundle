<?php

namespace Siganushka\GenericBundle\Form\Type;

use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionDistrictType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('parent', null);
        $resolver->setAllowedTypes('parent', ['null', RegionInterface::class]);

        $resolver->setNormalizer('query_builder', function (Options $options) {
            $queryBuilder = $options['em']->getRepository($options['class'])
                ->createQueryBuilder('r')
                ->where('r.parent = :parent')
                ->setParameter('parent', $options['parent'])
                ->addOrderBy('r.parent', 'ASC')
                ->addOrderBy('r.id', 'ASC');

            return $queryBuilder;
        });
    }

    public function getParent()
    {
        return RegionType::class;
    }
}
