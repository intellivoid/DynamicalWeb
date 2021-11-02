<?php


    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\WebApplicationException;

    /**
     * Class Actions
     * @package DynamicalWeb
     */
    class Actions
    {
        /**
         * Redirects the client to another location, this only
         * works if the server hasn't sent any data back yet
         *
         * Using this function will terminate the process
         *
         * @param string $location
         * @throws WebApplicationException
         */
        public static function redirect(string $location)
        {
            $requestHandler = DynamicalWeb::activeRequestHandler();
            $requestHandler->Redirect = true;
            $requestHandler->RedirectLocation = $location;
            $requestHandler->RedirectTime = 0;
            DynamicalWeb::activeRequestHandler($requestHandler);
        }

        /**
         * Same as redirect but with a delay, this function will
         * terminate the process
         *
         * @param string $location
         * @param int $time
         * @throws WebApplicationException
         */
        public static function delayedRedirect(string $location, int $time)
        {
            $requestHandler = DynamicalWeb::activeRequestHandler();
            $requestHandler->Redirect = true;
            $requestHandler->RedirectLocation = $location;
            $requestHandler->RedirectTime = $time;
            DynamicalWeb::activeRequestHandler($requestHandler);
        }
    }