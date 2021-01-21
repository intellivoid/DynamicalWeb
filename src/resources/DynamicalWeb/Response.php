<?php


    namespace DynamicalWeb;

    /**
     * Class Response
     * @package DynamicalWeb
     */
    class Response
    {
        /**
         * The headers that will be returned at response
         *
         * @var array
         */
        private static $Headers = [];

        /**
         * The response code from the server
         *
         * @var int
         */
        private static $ResponseCode = 200;

        /**
         * Returns the appropriate headers to connection
         *
         * @return bool
         */
        public static function connectionSetHeaders(): bool
        {
            $sapi_type = php_sapi_name();
            http_response_code(self::$ResponseCode);

            foreach(self::$Headers as $name => $value)
            {
                header("$name: $value");
            }

            return true;
        }

        /**
         * Starts the request stream
         *
         * @return bool
         */
        public static function startRequest(): bool
        {
            if(BufferStream::bufferOutputEnabled() == false)
            {
                self::connectionSetHeaders();
            }
            else
            {
                BufferStream::startStream();
            }

            return true;
        }

        /**
         * Finalizes the requests, flushes all the headers and response code and checks
         * if the content needs to be returned from a buffer stream or not
         *
         * Returns the page content if buffer_stream is enabled.
         *
         * @return string|null
         */
        public static function finishRequest(): ?string
        {
            if(BufferStream::bufferOutputEnabled())
            {
                BufferStream::endStream();
                self::connectionSetHeaders();
                print(BufferStream::getContent());
                return BufferStream::getContent();
            }

            return null;
        }

        /**
         * Sets a header that will be returned in the response
         *
         * @param string $name
         * @param string $value
         * @return bool
         */
        public static function setHeader(string $name, string $value): bool
        {
            self::$Headers[$name] = $value;

            return True;
        }

        /**
         * Get the current headers that are set
         *
         * @return array
         */
        public static function getHeaders(): array
        {
            return self::$Headers;
        }

        /**
         * Sets the response type of the content
         *
         * @param string $response_type
         * @return bool
         */
        public static function setResponseType(string $response_type): bool
        {
            return self::setHeader("Content-Type", $response_type);
        }

        /**
         * @param int $response_code
         * @return bool
         */
        public static function setResponseCode(int $response_code): bool
        {
            self::$ResponseCode = $response_code;
            if(BufferStream::bufferOutputEnabled() == false)
            {
                http_response_code($response_code);
            }
            return true;
        }


    }