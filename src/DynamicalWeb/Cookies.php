<?php

    namespace DynamicalWeb;

    use acm2\Exceptions\ConfigurationNotDefinedException;
    use DynamicalWeb\Classes\Utilities;
    use DynamicalWeb\CookieStorage\MySqlCookieStorage;
    use DynamicalWeb\CookieStorage\RedisCookieStorage;
    use DynamicalWeb\Exceptions\CookieStorageNotFoundException;
    use DynamicalWeb\Exceptions\StorageDriverException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Interfaces\CookieStorageDriver;
    use DynamicalWeb\Objects\Cookie;
    use DynamicalWeb\Objects\CookieStorage;

    class Cookies
    {
        /**
         * The storage driver for storage cookies
         *
         * @var null|CookieStorageDriver
         */
        private static $StorageDriver = null;

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

            if(isset($_COOKIE[$name]))
                return $_COOKIE[$name];
            if(isset(DynamicalWeb::activeRequestHandler()->Cookies[$name]))
                return DynamicalWeb::activeRequestHandler()->Cookies[$name];
            return null;
        }

        /**
         * Constructs and returns the cookie storage driver
         *
         * @return CookieStorageDriver
         * @throws ConfigurationNotDefinedException
         * @throws StorageDriverException
         */
        private static function getStorageDriver(): CookieStorageDriver
        {
            if(self::$StorageDriver == null)
            {
                $CookieStorageConfiguration = DynamicalWeb::getConfiguration('CookieStorage');
                switch(strtolower($CookieStorageConfiguration['Driver']))
                {
                    case 'mysql':
                        self::$StorageDriver = new MySqlCookieStorage();
                        break;

                    case 'redis':
                        self::$StorageDriver = new RedisCookieStorage();
                        break;

                    default:
                        throw new StorageDriverException('The storage driver \'' . $CookieStorageConfiguration['Driver'] . '\' is not supported');
                }
            }

            return self::$StorageDriver;
        }

        /**
         * Creates a new storage cookie instance, sets the cookie automatically.
         *
         * @param Cookie $cookie
         * @param bool $ip_tied
         * @return CookieStorage
         * @throws StorageDriverException
         * @throws WebApplicationException
         * @throws ConfigurationNotDefinedException
         */
        public static function createCookieStorage(Cookie &$cookie, bool $ip_tied=false): CookieStorage
        {
            $CookieStorage = CookieStorage::fromCookie(
                $cookie,
                DYNAMICAL_APP_NAME_SAFE,
                DYNAMICAL_CLIENT_IP_ADDRESS,
                $ip_tied
            );

            $CookieStorage = self::getStorageDriver()->set($CookieStorage);
            self::setCookie($CookieStorage->Cookie);
            DynamicalWeb::setMemoryObject($CookieStorage->getUniqueIdentifier(DYNAMICAL_APP_NAME_SAFE), $CookieStorage);
            return $CookieStorage;
        }

        /**
         * Returns an existing cookie storage object
         *
         * @param string $name
         * @return CookieStorage
         * @throws ConfigurationNotDefinedException
         * @throws CookieStorageNotFoundException
         * @throws StorageDriverException
         * @throws WebApplicationException
         * @noinspection PhpRedundantCatchClauseInspection
         */
        public static function getCookieStorage(string $name): CookieStorage
        {
            $CookieToken = self::getCookie($name);
            if($CookieToken == null)
                throw new CookieStorageNotFoundException('The cookie storage was not found (UndefinedCookie)');


            try
            {
                $CookieStorage = self::getStorageDriver()->get(
                    DYNAMICAL_APP_NAME_SAFE,
                    $name, $CookieToken
                );
            }
            catch(CookieStorageNotFoundException $e)
            {
                $CookieStorage = DynamicalWeb::getMemoryObject(Utilities::cookieIdentifier(
                    DYNAMICAL_APP_NAME_SAFE, $name, $CookieToken
                ));

                if($CookieStorage == null)
                    throw $e;
            }

            return $CookieStorage;

        }

        /**
         * Updates an existing cookie storage object
         *
         * @param CookieStorage $cookieStorage
         * @return CookieStorage
         * @throws ConfigurationNotDefinedException
         * @throws StorageDriverException
         */
        public static function updateCookieStorage(CookieStorage &$cookieStorage): CookieStorage
        {
            $cookieStorage = self::getStorageDriver()->update($cookieStorage);
            DynamicalWeb::setMemoryObject($cookieStorage->getUniqueIdentifier(DYNAMICAL_APP_NAME_SAFE), $cookieStorage);
            return $cookieStorage;
        }
    }