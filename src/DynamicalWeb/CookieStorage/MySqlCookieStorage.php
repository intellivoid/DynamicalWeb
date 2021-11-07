<?php

    namespace DynamicalWeb\CookieStorage;

    use acm2\Exceptions\ConfigurationNotDefinedException;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\CookieStorageNotFoundException;
    use DynamicalWeb\Exceptions\StorageDriverException;
    use DynamicalWeb\Interfaces\CookieStorageDriver;
    use DynamicalWeb\Objects\CookieStorage;
    use msqg\QueryBuilder;
    use mysqli;
    use ZiProto\ZiProto;

    class MySqlCookieStorage implements CookieStorageDriver
    {

        /**
         * @var mysqli
         */
        private mysqli $Mysqli;

        /**
         * @throws StorageDriverException
         * @throws ConfigurationNotDefinedException
         */
        public function __construct()
        {
            $CookieStorageConfiguration = DynamicalWeb::getConfiguration('CookieStorage');
            $this->Mysqli = new mysqli(
                $CookieStorageConfiguration['Host'],
                $CookieStorageConfiguration['Username'],
                $CookieStorageConfiguration['Password'],
                $CookieStorageConfiguration['Name'],
                $CookieStorageConfiguration['Port']
            );

            if($this->Mysqli->connect_error !== null)
                throw new StorageDriverException('There was an error while trying to connect to the MySQL server, ' . $this->Mysqli->connect_error, $this->Mysqli->connect_errno);
        }

        /**
         * Declares the MySQL table if not found
         *
         * @throws StorageDriverException
         */
        private function declareTable()
        {
            $TableQuery = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'cookie_storage.sql');
            $QueryResults = $this->Mysqli->query($TableQuery);
            if($QueryResults == false)
                throw new StorageDriverException('Unable to declare table \'cookie_storage\', ' . $this->Mysqli->error, $this->Mysqli->errno);
        }

        /**
         * Sets a cookie storage object to the MySQL storage driver
         *
         * @param CookieStorage $cookieStorage
         * @param bool $declare_table
         * @return CookieStorage
         * @throws StorageDriverException
         * @noinspection PhpCastIsUnnecessaryInspection
         */
        public function set(CookieStorage $cookieStorage, bool $declare_table=true): CookieStorage
        {
            $cookieStorage->LastUpdatedTimestamp = (int)time();

            $Query = QueryBuilder::insert_into('cookie_storage', [
                'web_application' => $this->Mysqli->real_escape_string($cookieStorage->WebApplication),
                'name' => $this->Mysqli->real_escape_string($cookieStorage->Cookie->Name),
                'token' => $this->Mysqli->real_escape_string($cookieStorage->Token),
                'expiry_time' => (int)$cookieStorage->Cookie->ExpiryTime,
                'path' => $this->Mysqli->real_escape_string($cookieStorage->Cookie->Path),
                'domain' => $this->Mysqli->real_escape_string($cookieStorage->Cookie->Domain),
                'http_only' => (int)$cookieStorage->Cookie->HttpOnly,
                'secure_only' => (int)$cookieStorage->Cookie->SecureOnly,
                'same_site_restriction' => $this->Mysqli->real_escape_string($cookieStorage->Cookie->SameSiteRestriction),
                'data' => $this->Mysqli->real_escape_string(ZiProto::encode($cookieStorage->Data)),
                'ip_address' => $this->Mysqli->real_escape_string($cookieStorage->IpAddress),
                'ip_tied' => (bool)$cookieStorage->IpTied,
                'last_updated_timestamp' => (int)$cookieStorage->LastUpdatedTimestamp,
                'created_timestamp' => (int)$cookieStorage->CreatedTimestamp
            ], true);

            $QueryResults = $this->Mysqli->query($Query);
            if($QueryResults == false)
            {
                if($this->Mysqli->errno == 1146 && $declare_table)
                {
                    $this->declareTable();
                    return $this->set($cookieStorage, false);
                }

                throw new StorageDriverException('Cannot set cookie to MySQL database, ' . $this->Mysqli->error, $this->Mysqli->errno);
            }

            return $cookieStorage;
        }

        /**
         * Returns an existing cookie storage object from the storage driver
         *
         * @param string $web_application_name
         * @param string $name
         * @param string $token
         * @param bool $declare_table
         * @return CookieStorage
         * @throws CookieStorageNotFoundException
         * @throws StorageDriverException
         */
        public function get(string $web_application_name, string $name, string $token, bool $declare_table=true): CookieStorage
        {
            $web_application_name = $this->Mysqli->real_escape_string($web_application_name);
            $name = $this->Mysqli->real_escape_string($name);
            $token = $this->Mysqli->real_escape_string($token);

            $Query = "SELECT web_application, name, token, expiry_time, path, domain, http_only, secure_only, same_site_restriction, data, ip_address, ip_tied, last_updated_timestamp, created_timestamp FROM `cookie_storage` WHERE web_application='$web_application_name' AND name='$name' AND token='$token';";
            $QueryResults = $this->Mysqli->query($Query);
            if($QueryResults == false)
            {
                if($this->Mysqli->errno == 1146 && $declare_table)
                {
                    $this->declareTable();
                    return $this->get($web_application_name, $name, $token, false);
                }

                throw new StorageDriverException('Cannot get cookie from MySQL database, ' . $this->Mysqli->error, $this->Mysqli->errno);
            }

            $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);

            if ($Row == False)
            {
                throw new CookieStorageNotFoundException('The requested cookie storage \'' . $name . '\' was not found');
            }

            $Row['data'] = ZiProto::decode($Row['data']);
            return(CookieStorage::fromArray($Row));
        }

        /**
         * Updates an existing storage cookie on the database
         *
         * @param CookieStorage $cookieStorage
         * @param bool $declare_table
         * @return CookieStorage
         * @throws StorageDriverException
         * @noinspection PhpCastIsUnnecessaryInspection
         */
        public function update(CookieStorage $cookieStorage, bool $declare_table=true): CookieStorage
        {
            $token = $this->Mysqli->real_escape_string($cookieStorage->Token);
            $expiry_time = (int)$cookieStorage->Cookie->ExpiryTime;
            $ip_address = $this->Mysqli->real_escape_string($cookieStorage->IpAddress);
            $ip_tied = (int)$cookieStorage->IpTied;
            $last_updated_timestamp = (int)time();
            $cookieStorage->LastUpdatedTimestamp = $last_updated_timestamp;
            $web_application_name = $this->Mysqli->real_escape_string($cookieStorage->WebApplication);
            $name = $this->Mysqli->real_escape_string($cookieStorage->Cookie->Name);

            $data = $this->Mysqli->real_escape_string(ZiProto::encode($cookieStorage->Data));

            $Query = "UPDATE `cookie_storage` SET token='$token', expiry_time=$expiry_time, ip_address='$ip_address', data='$data', ip_tied=$ip_tied, last_updated_timestamp=$last_updated_timestamp WHERE web_application='$web_application_name' AND token='$token' AND name='$name';";
            $QueryResults = $this->Mysqli->query($Query);

            if($QueryResults == false)
            {
                if($this->Mysqli->errno == 1146 && $declare_table)
                {
                    $this->declareTable();
                    return $this->update($cookieStorage, false);
                }

                throw new StorageDriverException('Cannot update cookie to MySQL database, ' . $this->Mysqli->error, $this->Mysqli->errno);
            }

            return $cookieStorage;
        }
    }