<?php

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\FileNotFoundException;
    use DynamicalWeb\Objects\RequestHandler;
    use DynamicalWeb\Objects\WebApplication\Configuration;

    class Utilities
    {
        /**
         * Returns the user agent regexes data
         *
         * @return array
         * @throws FileNotFoundException
         */
        public static function getUserAgentRegexes(): array
        {
            $file_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'regexes.json';

            if(file_exists($file_path) == false)
                throw new FileNotFoundException('The regexes for parsing user agents was not found', $file_path);

            return json_decode(file_get_contents($file_path), true);
        }

        /**
         * Returns the absolute path
         *
         * @param $path
         * @return string
         */
        public static function getAbsolutePath($path)
        {
            $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
            $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
            $absolutes = array();
            foreach ($parts as $part)
            {
                if ('.' == $part) continue;
                if ('..' == $part)
                {
                    array_pop($absolutes);
                }
                else
                {
                    $absolutes[] = $part;
                }
            }
            return implode(DIRECTORY_SEPARATOR, $absolutes);
        }

        /**
         * Transmits all the headers from the request handler
         *
         * @param RequestHandler $handler
         */
        public static function processHeaders(RequestHandler $handler)
        {
            // 1. Response code and content-type
            http_response_code($handler->getResponseCode());

            $FinalHeaders = [
                'Content-Type' => $handler->getResponseContentType()
            ];

            // 2. Other headers
            $FinalHeaders = array_merge($FinalHeaders, $handler->getResponseHeaders());

            // Process signatures and custom headers
            /** @var Configuration $WebAppConfiguration */
            $WebAppConfiguration = DynamicalWeb::getMemoryObject('app_configuration');
            if($WebAppConfiguration->FrameworkSignature)
                $FinalHeaders = array_merge($FinalHeaders, DynamicalWeb::getServerHeaders());
            if($WebAppConfiguration->ApplicationSignature)
                $FinalHeaders = array_merge($FinalHeaders, WebApplication::getApplicationHeaders());
            $FinalHeaders = array_merge($FinalHeaders, $WebAppConfiguration->Headers);

            // Finally, send all the headers
            foreach($FinalHeaders as $header => $value)
                header("$header: $value");
        }

        /**
         * Transmits the content size header
         *
         * @param int $size
         */
        public static function setContentSize(int $size)
        {
            header('Content-Length: ' . $size);
        }
    }