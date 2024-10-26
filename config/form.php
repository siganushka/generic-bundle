<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Form\Extension\CollectionTypeExtension;
use Siganushka\GenericBundle\Form\Extension\Html5ValidationTypeExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.form.type_extension.collection', CollectionTypeExtension::class)
            ->tag('form.type_extension')

        ->set('siganushka_generic.form.type_extension.html5_validation', Html5ValidationTypeExtension::class)
            ->tag('form.type_extension')
    ;
};
