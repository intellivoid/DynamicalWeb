<?php

    namespace DynamicalWeb\Classes;

    class Validate
    {
        /**
         * Validates the cookie name
         *
         * @param $name
         * @return bool
         * @noinspection PhpUnused
         */
        public static function cookieName($name): bool
        {
            $name = (string) $name;

            // https://bugs.php.net/bug.php?id=69523
            if ($name !== '' || PHP_VERSION_ID < 70000)
            {
                if (!preg_match('/[=,; \\t\\r\\n\\013\\014]/', $name))
                    return true;
            }

            return false;
        }

        /**
         * Determines if expiry time is valid for a cookie
         *
         * @param $expiryTime
         * @return bool
         * @noinspection PhpUnused
         */
        public static function cookieExpiryTimeValid($expiryTime): bool
        {
            return is_numeric($expiryTime) || is_null($expiryTime) || is_bool($expiryTime);
        }

        /**
         * Calculates the max age
         *
         * @param $expiryTime
         * @return int
         */
        public static function calculateMaxAge($expiryTime): int
        {
            if ($expiryTime === 0)
                return 0;

            $maxAge = $expiryTime - time();

            // The value of the `Max-Age` property must not be negative on PHP 7.0.19+ (< 7.1) and
            // PHP 7.1.5+ (https://bugs.php.net/bug.php?id=72071).
            if ((PHP_VERSION_ID >= 70019 && PHP_VERSION_ID < 70100) || PHP_VERSION_ID >= 70105)
            {
                if ($maxAge < 0)
                    $maxAge = 0;
            }

            return $maxAge;
        }

        /**
         * Formats the expiration time for a cookie header
         *
         * @param int $expiryTime
         * @param false $forceShow
         * @return string|null
         * @noinspection PhpMissingReturnTypeInspection
         * @noinspection PhpUnused
         */
        public static function formatExpiryTime(int $expiryTime, bool $forceShow=false)
        {
            if ($expiryTime > 0 || $forceShow)
            {
                if ($forceShow)
                    $expiryTime = 1;
                return gmdate('D, d-M-Y H:i:s T', $expiryTime);
            }
            else
            {
                return null;
            }
        }

        /**
         * @param int $expiryTime
         * @param bool $forceShow
         * @return string|null
         * @noinspection PhpUnused
         */
        public static function formatMaxAge(int $expiryTime, bool $forceShow=false): ?string
        {
            if ($expiryTime > 0 || $forceShow)
            {
                return (string) self::calculateMaxAge($expiryTime);
            }
            else
            {
                return null;
            }
        }

        /**
         * Normalizes a domain name
         *
         * @param string|null $domain
         * @return string|null
         * @noinspection PhpUnused
         */
        public static function normalizeDomain(?string $domain=null): ?string
        {
            // make sure that the domain is a string
            $domain = (string) $domain;

            // if the cookie should be valid for the current host only
            if ($domain === '')
                // no need for further normalization
                return null;

            // if the provided domain is actually an IP address
            if (filter_var($domain, FILTER_VALIDATE_IP) !== false)
                // let the cookie be valid for the current host
                return null;

            // for local hostnames (which either have no dot at all or a leading dot only)
            /** @noinspection PhpStrFunctionsInspection */
            if (strpos($domain, '.') === false || strrpos($domain, '.') === 0)
                // let the cookie be valid for the current host while ensuring maximum compatibility
                return null;

            // unless the domain already starts with a dot
            if ($domain[0] !== '.')
                // prepend a dot for maximum compatibility (e.g. with RFC 2109)
                $domain = '.' . $domain;

            // return the normalized domain
            return $domain;
        }
    }