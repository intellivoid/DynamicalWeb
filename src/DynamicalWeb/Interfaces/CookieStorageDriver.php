<?php

    namespace DynamicalWeb\Interfaces;

    use DynamicalWeb\Objects\CookieStorage;

    interface CookieStorageDriver
    {
        /**
         * Sets a cookie storage object to the storage driver
         *
         * @param CookieStorage $cookieStorage
         * @return CookieStorage
         */
        public function set(CookieStorage $cookieStorage): CookieStorage;

        /**
         * Gets an existing storage cookie object from the storage driver
         *
         * @param string $web_application_name
         * @param string $name
         * @param string $token
         * @return CookieStorage
         */
        public function get(string $web_application_name, string $name, string $token): CookieStorage;

        /**
         * Updates an existing cookie storage object on the storage driver
         *
         * @param CookieStorage $cookieStorage
         * @return CookieStorage
         */
        public function update(CookieStorage $cookieStorage): CookieStorage;
    }