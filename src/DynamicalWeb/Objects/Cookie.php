<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\SameSiteRestriction;
    use DynamicalWeb\Classes\Validate;
    use DynamicalWeb\Exceptions\InvalidCookieException;

    class Cookie
    {
        /**
         * the name of the cookie which is also the key for future accesses via `$_COOKIE[...]`
         *
         * @var string
         */
        public $Name;

        /**
         * the value of the cookie that will be stored on the client's machine
         *
         * @var null|mixed
         */
        public $Value;

        /**
         * the Unix timestamp indicating the time that the cookie will expire at, i.e. usually `time() + $seconds`
         *
         * @var int
         */
        public $ExpiryTime;

        /**
         * the path on the server that the cookie will be valid for (including all sub-directories), e.g. an empty
         * string for the current directory or `/` for the root directory
         *
         * @var string
         */
        public $Path;

        /**
         * the domain that the cookie will be valid for (including subdomains) or `null` for the current host
         * (excluding subdomains)
         *
         * @var string|null
         */
        public $Domain;

        /**
         * indicates that the cookie should be accessible through the HTTP protocol only and not through scripting languages
         *
         * @var bool
         */
        public $HttpOnly;

        /**
         * indicates that the cookie should be sent back by the client over secure HTTPS connections only
         *
         * @var bool
         */
        public $SecureOnly;

        /**
         * indicates that the cookie should not be sent along with cross-site requests
         * (either `null`, `None`, `Lax` or `Strict`)
         *
         * @var string
         */
        public $SameSiteRestriction;

        /**
         * @param $name
         * @param null $value
         */
        public function __construct($name, $value=null)
        {
            $this->Name = $name;
            $this->Value = $value;
            $this->ExpiryTime = 0;
            $this->Path = '/';
            $this->Domain = null;
            $this->HttpOnly = true;
            $this->SecureOnly = false;
            $this->SameSiteRestriction = SameSiteRestriction::Lax;
        }
    }