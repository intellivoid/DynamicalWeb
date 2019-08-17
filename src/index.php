<?php
    /**
     * DynamicalWeb Bootstrap v1.0.0.1
     */

    // Load the application resources
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Language;
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

        header('Location: '. APP_HOME_PAGE);
        exit();
    }

    if(isset($_GET['c_view_point']) == false)
    {
        Page::load(APP_HOME_PAGE);
    }
    else
    {
        if(strstr($_GET['c_view_point'], '/'))
        {
            Page::load('404');
        }
        else
        {
            Page::load($_GET['c_view_point']);
        }
    }

    Runtime::runEventScripts('after_request');
