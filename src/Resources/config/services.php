<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Siganushka\GenericBundle\Doctrine\EventListener\SortableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;

return static function (ContainerConfigurator $container) {
    foreach ([
        'timestampable' => TimestampableListener::class,
        'sortable' => SortableListener::class,
    ] as $alias => $className) {
        $fullAlias = sprintf('siganushka_generic.doctrine.%s_listener', $alias);
        $container->services()
            ->set($fullAlias, $className)
            ->tag('doctrine.event_subscriber')
        ;
    }
};
