<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Classes\Utilities;
    use Symfony\Component\Uid\Uuid;

    class CookieStorage
    {
        /**
         * The name of the web application that owns this cookie
         *
         * @var string
         */
        public $WebApplication;

        /**
         * The cookie used to identity this cookie storage object
         *
         * @var Cookie
         */
        public $Cookie;

        /**
         * The token used for the cookie identification
         *
         * @var string
         */
        public $Token;

        /**
         * The data storage for this cookie
         *
         * @var array
         */
        public $Data;

        /**
         * Indicates if the storage is IP Tied
         *
         * @var bool
         */
        public $IpTied;

        /**
         * The IP address of the client
         *
         * @var string
         */
        public $IpAddress;

        /**
         * The Unix Timestamp for when the cookie storage object was last updated
         *
         * @var int
         */
        public $LastUpdatedTimestamp;

        /**
         * The Unix Timestamp for when the cookie storage object was created
         *
         * @var int
         */
        public $CreatedTimestamp;

        /**
         * Gets a unique identifier for the cookie
         *
         * @return string
         */
        public function getUniqueIdentifier(string $web_application): string
        {
            return Utilities::cookieIdentifier($web_application, $this->Cookie->Name, $this->Token);
        }

        /**
         * Constructs a CookieStorage object from a cookie (Assigns a Token to the cookie value)
         *
         * @param Cookie $cookie
         * @param string $web_application_name
         * @param string $ip_address
         * @param bool $ip_tied
         * @return CookieStorage
         */
        public static function fromCookie(Cookie $cookie, string $web_application_name, string $ip_address, bool $ip_tied=false)
        {
            $CookieStorageObject = new CookieStorage();

            $cookie->Value = Uuid::v4()->toRfc4122();
            $CookieStorageObject->Cookie = $cookie;
            $CookieStorageObject->WebApplication = $web_application_name;
            $CookieStorageObject->LastUpdatedTimestamp = (int)time();
            $CookieStorageObject->IpAddress = $ip_address;
            $CookieStorageObject->IpTied = $ip_tied;
            $CookieStorageObject->CreatedTimestamp = (int)time();
            $CookieStorageObject->Data = [];
            $CookieStorageObject->Token = $cookie->Value;

            return $CookieStorageObject;
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray()
        {
            return [
                'web_application' => $this->WebApplication,
                'name' => $this->Cookie->Name,
                'token' => $this->Token,
                'expiry_time' => $this->Cookie->ExpiryTime,
                'path' => $this->Cookie->Path,
                'domain' => $this->Cookie->Domain,
                'http_only' => $this->Cookie->HttpOnly,
                'secure_only' => $this->Cookie->SecureOnly,
                'same_site_restriction' => $this->Cookie->SameSiteRestriction,
                'data' => $this->Data,
                'ip_address' => $this->IpAddress,
                'ip_tied' => $this->IpTied,
                'last_updated_timestamp' => $this->LastUpdatedTimestamp,
                'created_timestamp' => $this->CreatedTimestamp
            ];
        }

        /**8
         * @param array $data
         * @return CookieStorage
         */
        public static function fromArray(array $data): CookieStorage
        {
            $CookieStorageObject = new CookieStorage();
            $CookieStorageObject->Cookie = new Cookie($data['name']);

            if(isset($data['web_application']))
                $CookieStorageObject->WebApplication = $data['web_application'];

            if(isset($data['name']))
                $CookieStorageObject->Cookie->Name = $data['name'];

            if(isset($data['token']))
                $CookieStorageObject->Token = $data['token'];

            if(isset($data['expiry_time']))
                $CookieStorageObject->Cookie->ExpiryTime = (int)$data['expiry_time'];

            if(isset($data['path']))
                $CookieStorageObject->Cookie->Path = $data['path'];

            if(isset($data['domain']))
            {
                if(strlen($data['domain']) == 0)
                {
                    $CookieStorageObject->Cookie->Domain = null;
                }
                else
                {
                    $CookieStorageObject->Cookie->Domain = $data['domain'];
                }
            }

            if(isset($data['http_only']))
                $CookieStorageObject->Cookie->HttpOnly = (bool)$data['http_only'];

            if(isset($data['secure_only']))
                $CookieStorageObject->Cookie->SecureOnly = (bool)$data['secure_only'];

            if(isset($data['same_site_restriction']))
                $CookieStorageObject->Cookie->SameSiteRestriction = $data['same_site_restriction'];

            if(isset($data['data']))
                $CookieStorageObject->Data = $data['data'];

            if(isset($data['ip_address']))
                $CookieStorageObject->IpAddress = $data['ip_address'];

            if(isset($data['ip_tied']))
                $CookieStorageObject->IpTied = (bool)$data['ip_tied'];

            if(isset($data['last_updated_timestamp']))
                $CookieStorageObject->LastUpdatedTimestamp = (int)$data['last_updated_timestamp'];

            if(isset($data['created_timestamp']))
                $CookieStorageObject->CreatedTimestamp = (int)$data['created_timestamp'];

            return $CookieStorageObject;
        }
    }