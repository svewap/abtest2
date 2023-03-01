<?php

declare(strict_types=1);

namespace DanielSiepmann\Configuration;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WerkraumMedia\ABTest\Switcher;
use WerkraumMedia\ABTest\TCA\VariantFilter;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('WerkraumMedia\\ABTest\\', '../Classes/');

    $services->set(Switcher::class)->public();
    $services->set(VariantFilter::class)->public();
};
