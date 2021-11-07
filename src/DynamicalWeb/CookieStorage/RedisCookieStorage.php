<?php

    namespace DynamicalWeb\CookieStorage;

    use acm2\Exceptions\ConfigurationNotDefinedException;
    use DynamicalWeb\Classes\Utilities;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\CookieStorageNotFoundException;
    use DynamicalWeb\Interfaces\CookieStorageDriver;
    use DynamicalWeb\Objects\CookieStorage;
    use PpmZiProto\ZiProto;
    use Redis;

    class RedisCookieStorage implements CookieStorageDriver
    {
        /**
         * @var Redis
         */
        private Redis $Redis;

        /**
         * @throws ConfigurationNotDefinedException
         */
        public function __construct()
        {
            $CookieStorageConfiguration = DynamicalWeb::getConfiguration('CookieStorage');
            $this->Redis = new Redis();
            $this->Redis->connect(
                $CookieStorageConfiguration['Host'],
                $CookieStorageConfiguration['Port'],
            );

            if($CookieStorageConfiguration['AuthenticationEnabled'])
                $this->Redis->auth($CookieStorageConfiguration['Password']);
        }

        /**
         * Sets a cookie storage object to the current storage
         *
         * @param CookieStorage $cookieStorage
         * @return CookieStorage
         */
        public function set(CookieStorage $cookieStorage): CookieStorage
        {
            $cookieStorage->LastUpdatedTimestamp = time();

            if($cookieStorage->Cookie->ExpiryTime > 0)
            {
                $this->Redis->set(
                    $cookieStorage->getUniqueIdentifier($cookieStorage->WebApplication),
                    ZiProto::encode($cookieStorage->toArray()), $cookieStorage->Cookie->ExpiryTime + 10
                );

            }
            else
            {
                $this->Redis->set(
                    $cookieStorage->getUniqueIdentifier($cookieStorage->WebApplication),
                    ZiProto::encode($cookieStorage->toArray()), 1209600
                );
            }

            return $cookieStorage;
        }

        /**
         * Gets an existing cookie storage item from the storage driver
         *
         * @param string $web_application_name
         * @param string $name
         * @param string $token
         * @return CookieStorage
         * @throws CookieStorageNotFoundException
         */
        public function get(string $web_application_name, string $name, string $token): CookieStorage
        {
            $UniqueIdentifier = Utilities::cookieIdentifier($web_application_name, $name, $token);

            $CookieStorageResults = $this->Redis->get($UniqueIdentifier);
            if($CookieStorageResults == false)
                throw new CookieStorageNotFoundException('The requested cookie storage for \'' . $name . '\' was not found');

            return CookieStorage::fromArray(ZiProto::decode($CookieStorageResults));
        }

        /**
         * Updates an existing cookie storage object
         *
         * @param CookieStorage $cookieStorage
         * @return CookieStorage
         */
        public function update(CookieStorage $cookieStorage): CookieStorage
        {
            $cookieStorage->LastUpdatedTimestamp = time();
            $this->Redis->del($cookieStorage->getUniqueIdentifier(DYNAMICAL_APP_NAME_SAFE));
            return $this->set($cookieStorage);
        }
    }