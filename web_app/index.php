<?php

    require('ppm');
    import('net.intellivoid.dynamical_web');

    $WebApplication = new \DynamicalWeb\Classes\WebApplication(__DIR__ . DIRECTORY_SEPARATOR . 'resources');
    $WebApplication->initialize();

    print("<pre>" . json_encode(\DynamicalWeb\DynamicalWeb::getDefinitions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>");