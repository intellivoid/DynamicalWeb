<?php

    namespace DynamicalWeb;

    use Exception;
    use RuntimeException;

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
         * @throws Exception
         * @return string|null Returns the output if buffer_output is enabled
         */
        public static function load(string $name): ?string
        {
            Response::startRequest();
            Response::setResponseType("text/html; charset=UTF-8");

            if(self::exists($name) == false)
            {
                Response::setResponseCode(404);

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
                    define('APP_CURRENT_PAGE', '404');
                    define('APP_CURRENT_PAGE_DIRECTORY', APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . '404');

                    Runtime::runEventScripts('on_page_load');
                    Language::loadPage('404');
                    /** @noinspection PhpIncludeInspection */
                    include_once(APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'contents.php');
                    Runtime::runEventScripts('page_loaded');
                }

                return Response::finishRequest();
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

            return Response::finishRequest();
        }

        /**
         * Returns a static response to the client
         *
         * @param string $title
         * @param string $header
         * @param string $body
         * @return string|null Returns the output if buffer_output is enabled
         * @throws Exception
         */
        public static function staticResponse(string $title, string $header, string $body): ?string
        {
            /* START DT P2 DX000000181  kasper.medvedkov    Remove branding. */
            Response::startRequest();
            Response::setResponseType("text/html; charset=UTF-8");

            ?>
            <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
            <html lang="en">
                <head>
                    <meta charset="UTF-8"/>
                    <?PHP CSS::importStyle("generic"); ?>
                    <title><?PHP HTML::print($title); ?></title>
                </head>
                <body>
                    <div id="main_wrapper">
                        <h1><?PHP HTML::print($header); ?></h1>
                        <p><?PHP HTML::print($body, false); ?></p>
                        <hr/>
                        <address>DynamicalWeb/<?PHP HTML::print(DYNAMICAL_WEB_VERSION); ?></address>
                    </div>
                </body>
            </html>
            <?PHP
            /* END DT P2 DX000000181  kasper.medvedkov    Remove branding. */

            return Response::finishRequest();
        }

        /**
         * Returns a static response page but with error details
         *
         * @param string $title
         * @param string $header
         * @param string $body
         * @return string|null
         * @throws Exception
         */
        public static function staticErrorResponse(string $title, string $header, string $body): ?string
        {
            Response::startRequest();
            Response::setResponseType("text/html; charset=UTF-8");

            ?>
            <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
            <html lang="en">
            <head>
                <meta charset="UTF-8"/>
                <?PHP CSS::importStyle("generic"); ?>
                <title><?PHP HTML::print($title); ?></title>
            </head>
            <body>
            <div id="main_wrapper">
                <h1><?PHP HTML::print($header); ?></h1>
                <p>Debugging information regarding the exception can be found below</p>
                <div class="content">
                    <hr/>
                    <?PHP HTML::print($body, false); ?>
                    <hr/>
                </div>
                <hr/>
                <address>DynamicalWeb/<?PHP HTML::print(DYNAMICAL_WEB_VERSION); ?></address>
            </div>
            </body>
            </html>
            <?PHP

            return Response::finishRequest(true);
        }
    }
