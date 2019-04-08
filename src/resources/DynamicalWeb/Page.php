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
            $FormattedName = strtolower(stripslashes($name));
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
         */
        public static function load(string $name)
        {
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

                    Language::loadPage('404');
                    /** @noinspection PhpIncludeInspection */
                    include_once(APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'contents.php');
                }

                return ;
            }

            $FormattedName = strtolower(stripslashes($name));

            define('APP_CURRENT_PAGE', $FormattedName, false);
            define('APP_CURRENT_PAGE_DIRECTORY', APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . $FormattedName);

            Language::loadPage($FormattedName);
            /** @noinspection PhpIncludeInspection */
            include_once(APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'contents.php');

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
            ?>
            <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
            <html lang="en">
                <head>
                    <title><?PHP HTML::print($title); ?></title>
                </head>
                <body>
                    <h1><?PHP HTML::print($header); ?></h1>
                    <p><?PHP HTML::print($body); ?></p>
                    <hr>
                    <address>DynamicalWeb/1.0.0.0 (Intellivoid) Written by Zi Xing Narrakas</address>
                </body>
            </html>
            <?PHP
        }
    }