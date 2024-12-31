<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Form\Extension\ButtonTypeExtension;
use Siganushka\GenericBundle\Form\Extension\CollectionTypeExtension;
use Siganushka\GenericBundle\Form\Extension\Html5ValidationTypeExtension;
use Siganushka\GenericBundle\Form\Type\IdentifierEntityType;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.form.type_extension.collection', CollectionTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.button', ButtonTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.html5_validation', Html5ValidationTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type.identifier_entity', IdentifierEntityType::class)
            ->arg(0, service('doctrine'))
            ->tag('form.type')
    ;
};
