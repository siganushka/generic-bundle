<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Controller\FormController;
use Siganushka\GenericBundle\Form\Extension\ButtonTypeExtension;
use Siganushka\GenericBundle\Form\Extension\ChoiceTypeExtension;
use Siganushka\GenericBundle\Form\Extension\CollectionTypeExtension;
use Siganushka\GenericBundle\Form\Extension\CsrfTypeExtension;
use Siganushka\GenericBundle\Form\Extension\MoneyTypeExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.form.controller', FormController::class)
            ->arg('$formTypes', tagged_iterator('form.type'))
            ->tag('controller.service_arguments')

        ->set('siganushka_generic.form.csrf_type_extension', CsrfTypeExtension::class)
            ->arg('$requestStack', service('request_stack'))
            ->tag('form.type_extension', ['priority' => -128])

        ->set('siganushka_generic.form.money_type_extension', MoneyTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.button_type_extension', ButtonTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.choice_type_extension', ChoiceTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.collection_type_extension', CollectionTypeExtension::class)
            ->tag('form.type_extension')
    ;
};
