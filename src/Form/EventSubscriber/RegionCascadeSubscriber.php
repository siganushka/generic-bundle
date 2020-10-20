<?php

namespace Siganushka\GenericBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RegionCascadeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();

        $this->addCity($event, $data->getProvince());
        $this->addDistrict($event, $data->getCity());
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        $this->addCity($event, $data['province'] ?? null);
        $this->addDistrict($event, $data['city'] ?? null);
    }

    private function addCity(FormEvent $event, $province)
    {
        $form = $event->getForm();
        $form->add('city', RegionType::class, [
            'query_builder' => function ($er) use ($province) {
                return $er->createQueryBuilder('r')
                    ->where('r.parent = :parent')
                    ->setParameter('parent', $province)
                    ->addOrderBy('r.parent', 'ASC')
                    ->addOrderBy('r.id', 'ASC');
            },
        ]);
    }

    private function addDistrict(FormEvent $event, $city)
    {
        $form = $event->getForm();
        $form->add('district', RegionType::class, [
            'query_builder' => function ($er) use ($city) {
                return $er->createQueryBuilder('r')
                    ->where('r.parent = :parent')
                    ->setParameter('parent', $city)
                    ->addOrderBy('r.parent', 'ASC')
                    ->addOrderBy('r.id', 'ASC');
            },
        ]);
    }
}
