<?php

    require('ppm');
    import('net.intellivoid.dynamical_web');

    $WebApplication = new \DynamicalWeb\Classes\WebApplication('Web Application', __DIR__ . DIRECTORY_SEPARATOR . 'resources');
    $WebApplication->initialize();

    var_dump(\DynamicalWeb\DynamicalWeb::getClientRequest());