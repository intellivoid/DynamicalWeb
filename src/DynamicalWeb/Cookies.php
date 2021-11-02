<?php

    namespace DynamicalWeb;

    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\Cookie;

    class Cookies
    {
        /**
         * Sets a cookie to
         *
         * @param Cookie $cookie
         * @throws WebApplicationException
         */
        public static function setCookie(Cookie $cookie)
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('Cookies::setCookie() can only execute if the Web Application is initialized');

            $requestHandler = DynamicalWeb::activeRequestHandler();
            $requestHandler->CookiesToSet[$cookie->Name] = $cookie;
            DynamicalWeb::activeRequestHandler($requestHandler);
        }

        /**
         * @param string $name
         * @return string|null
         * @throws WebApplicationException
         */
        public static function getCookie(string $name): ?string
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('Cookies::getCookie() can only execute if the Web Application is initialized');

            if(isset(DynamicalWeb::activeRequestHandler()->Cookies[$name]) == false)
                return null;
            return DynamicalWeb::activeRequestHandler()->Cookies[$name];
        }
    }