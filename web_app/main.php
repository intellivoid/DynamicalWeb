<?php

    require('ppm');
    import('net.intellivoid.dynamical_web');

    $WebApplication = new \DynamicalWeb\Classes\WebApplication(__DIR__ . DIRECTORY_SEPARATOR . 'resources');
    $WebApplication->initialize();

    $request_handler = \DynamicalWeb\DynamicalWeb::getRequestHandler();
    $request_handler->execute();