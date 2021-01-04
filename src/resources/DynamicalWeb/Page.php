<?php

    namespace DynamicalWeb;

    /**
     * Class Page
     * @package DynamicalWeb
     */
    class Page
    {
        /**
         * Indicates if the page exists or not
         *
         * @param string $name
         * @return bool
         */
        public static function exists(string $name): bool
        {
            /* START DT P1 DX000000184  kasper.medvedkov    Prevent getting the page name lowercased. */
            $FormattedName = stripslashes($name);
            /* END DT P1 DX000000184  kasper.medvedkov    Prevent getting the page name lowercased. */

            $PageDirectory = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . $FormattedName;

            if(file_exists($PageDirectory) == false)
            {
                return false;
            }

            if(file_exists($PageDirectory . DIRECTORY_SEPARATOR . 'contents.php') == false)
            {
                return false;
            }

            return true;
        }

        /**
         * Loads the content for the requested page
         *
         * @param string $name
         * @throws \Exception
         */
        public static function load(string $name)
        {
            $ServerInformation = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'dynamicalweb.json');
            $ServerInformation = json_decode($ServerInformation, true);

            /* START DT P1 DX000000181  kasper.medvedkov    Remove branding. */
            header('X-Powered-By: DynamicalWeb/' . $ServerInformation['VERSION'] . '');
            header('X-DynamicalWeb-Version: ' . $ServerInformation['VERSION']);
            /* END DT P1 DX000000181  kasper.medvedkov    Remove branding. */

            if(self::exists($name) == false)
            {
                if(self::exists('404') == false)
                {
                    self::staticResponse(
                        'Not Found',
                        '404 Not Found',
                        'The page you were looking for was not found'
                    );
                }
                else
                {
                    define('APP_CURRENT_PAGE', '404', false);
                    define('APP_CURRENT_PAGE_DIRECTORY', APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . '404');

                    Runtime::runEventScripts('on_page_load');
                    Language::loadPage('404');
                    /** @noinspection PhpIncludeInspection */
                    include_once(APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'contents.php');
                    Runtime::runEventScripts('page_loaded');
                }

                return ;
            }
            
            /* START DT P2 DX000000184  kasper.medvedkov    Prevent getting the page name lowercased. */
            $FormattedName = stripslashes($name);
            /* END DT P2 DX000000184  kasper.medvedkov    Prevent getting the page name lowercased. */

            define('APP_CURRENT_PAGE', $FormattedName, false);
            define('APP_CURRENT_PAGE_DIRECTORY', APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . $FormattedName);

            Language::loadPage($FormattedName);

            Runtime::runEventScripts('on_page_load');
            /** @noinspection PhpIncludeInspection */
            include_once(APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'contents.php');

            Runtime::runEventScripts('page_loaded');
            return;
        }

        /**
         * Returns a static response to the client
         *
         * @param string $title
         * @param string $header
         * @param string $body
         */
        public static function staticResponse(string $title, string $header, string $body)
        {
            /* START DT P2 DX000000181  kasper.medvedkov    Remove branding. */
            ?>
            <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
            <html lang="en">
                <head>
                    <title><?PHP HTML::print($title); ?></title>
                </head>
                <body>
                    <h1><?PHP HTML::print($header); ?></h1>
                    <p><?PHP HTML::print($body, false); ?></p>
                    <hr>
                    <address>DynamicalWeb/<?PHP HTML::print(DYNAMICAL_WEB_VERSION); ?></address>
                </body>
            </html>
            <?PHP
            /* END DT P2 DX000000181  kasper.medvedkov    Remove branding. */

        }
    }
