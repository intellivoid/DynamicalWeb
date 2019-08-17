<?php


    namespace DynamicalWeb;

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
         */
        public function redirect(string $location)
        {
            header("Location: $location");
            exit();
        }
    }