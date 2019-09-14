<?php
    /**
     * DynamicalWeb Bootstrap v2.0.0.0
     */

    // Load the application resources
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Page;
    use DynamicalWeb\Runtime;

    require __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'DynamicalWeb' . DIRECTORY_SEPARATOR . 'DynamicalWeb.php';

    try
    {
        DynamicalWeb::loadApplication(__DIR__ . DIRECTORY_SEPARATOR . 'resources');
    }
    catch (Exception $e)
    {
        Page::staticResponse('DynamicalWeb Error', 'DynamicalWeb Internal Server Error', $e->getMessage());
        exit();
    }

    DynamicalWeb::defineVariables();
    Runtime::runEventScripts('on_request');
    DynamicalWeb::processRequest();
    Runtime::runEventScripts('after_request');