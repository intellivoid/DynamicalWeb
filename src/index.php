<?php
    /**
     * DynamicalWeb Bootstrap v1.0.0.0
     */

    // Load the application resources
    require __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'DynamicalWeb' . DIRECTORY_SEPARATOR . 'DynamicalWeb.php';
    \DynamicalWeb\DynamicalWeb::loadApplication(__DIR__ . DIRECTORY_SEPARATOR . 'resources');

    if(isset($_GET['set_language']))
    {
        \DynamicalWeb\Language::changeLanguage($_GET['set_language']);
        header('Location: '. APP_HOME_PAGE);
        exit();
    }

    if(isset($_GET['c_view_point']) == false)
    {
        \DynamicalWeb\Page::load(APP_HOME_PAGE);
    }
    else
    {
        if(strstr($_GET['c_view_point'], '/'))
        {
            \DynamicalWeb\Page::load('404');
        }
        else
        {
            \DynamicalWeb\Page::load($_GET['c_view_point']);
        }
    }

