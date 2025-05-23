<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Form\Extension\ButtonTypeExtension;
use Siganushka\GenericBundle\Form\Extension\ChoiceTypeExtension;
use Siganushka\GenericBundle\Form\Extension\CollectionTypeExtension;
use Siganushka\GenericBundle\Form\Extension\FormTypeExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.form.type_extension.form', FormTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.button', ButtonTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.choice', ChoiceTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.collection', CollectionTypeExtension::class)
            ->tag('form.type_extension')
    ;
};
