<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Form\Extension\DisableHtml5Validation;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('siganushka_generic.form.type_extension.disable_html5_validation', DisableHtml5Validation::class)
            ->tag('form.type_extension')
    ;
};
