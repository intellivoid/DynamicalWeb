<?php
    /**
     * DynamicalWeb Bootstrap v2.0.0.0
     */

    // Load the application resources
    use DynamicalWeb\Actions;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Language;
    use DynamicalWeb\Page;
    use DynamicalWeb\Runtime;
    use DynamicalWeb\Utilities;

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

    if(isset($_GET['set_language']))
    {
        try
        {
            Language::changeLanguage($_GET['set_language']);
        }
        catch (Exception $e)
        {
            Page::staticResponse('DynamicalWeb Error', 'DynamicalWeb Internal Server Error', $e->getMessage());
            exit();
        }

        Actions::redirect(APP_HOME_PAGE);
    }

    DynamicalWeb::processRequest();

    Runtime::runEventScripts('after_request');
