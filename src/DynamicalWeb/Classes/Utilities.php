<?php

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\FileNotFoundException;
    use DynamicalWeb\Objects\RequestHandler;
    use DynamicalWeb\Objects\WebApplication\Configuration;
    use Exception;

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
         * @noinspection PhpMissingReturnTypeInspection
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
         * Returns the headers for cache control
         *
         * @param bool $cache
         * @param bool $public
         * @param int $ttl
         * @param string|null $file_path
         * @return array
         */
        public static function getCacheControl(bool $cache=true, bool $public=true, int $ttl=86400, ?string $file_path=null): array
        {
            $returnHeaders = [];

            if($cache == false)
            {
                if($public)
                {
                    $returnHeaders['Cache-Control'] = 'no-cache, must-revalidate, public';
                }
                else
                {
                    $returnHeaders['Cache-Control'] = 'no-cache, must-revalidate, private';
                }

                $returnHeaders['Pragma'] = 'no-cache';
            }
            else
            {
                if($public)
                {
                    $returnHeaders['Cache-Control'] = 'public, immutable, max-age=' . $ttl;
                }
                else
                {
                    $returnHeaders['Cache-Control'] = 'private, immutable, max-age=' . $ttl;
                }
            }

            if($file_path !== null && file_exists($file_path))
            {
                $eTag = hash_file('crc32', $file_path);
                $returnHeaders['ETag'] = "\"$eTag\"";
            }

            return $returnHeaders;
        }

        /**
         * Transmits all the headers from the request handler
         *
         * @param RequestHandler $handler
         * @throws \DynamicalWeb\Exceptions\WebApplicationException
         */
        public static function processHeaders(RequestHandler $handler)
        {
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
            if($WebAppConfiguration->SecurityHeaders)
                $FinalHeaders = array_merge($FinalHeaders, DynamicalWeb::getSecurityHeaders());

            // Process cache headers and modify response code if Etag is a match or not
            if($handler->CacheResponse && ($handler->ResourceSource !== ResourceSource::Page || $handler->ResourceSource !== ResourceSource::Memory || $handler->ResourceSource !== ResourceSource::CompiledWebAsset))
            {
                $Public = true;
                if($handler->PrivateCache)
                    $Public = false;

                $FilePath = null;
                if(strlen($handler->Source) <= 256 && file_exists($handler->Source))
                    $FilePath = $handler->Source;

                $FinalHeaders = array_merge($FinalHeaders, Utilities::getCacheControl(true, $Public,  $handler->getCacheTtl(), $FilePath));

                if(isset($FinalHeaders['ETag']))
                {
                    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : null;
                    if($if_none_match !== null)
                    {
                        $tags = explode( ", ", $if_none_match);
                        foreach( $tags as $tag )
                        {
                            if( $tag == $FinalHeaders['ETag'] )
                            {
                                $handler->setResponseCode(304);
                                break;
                            }
                        }
                    }
                }

                if(isset($FinalHeaders['ETag']) && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $FinalHeaders['ETag'])
                    $handler->setResponseCode(304);

            }

            if($handler->ResourceSource == ResourceSource::Page || $handler->ResourceSource == ResourceSource::Memory)
            {
                $FinalHeaders = array_merge($FinalHeaders, Utilities::getCacheControl(false));

                if($handler->Redirect)
                {
                    if($handler->RedirectTime > 0)
                    {
                        $FinalHeaders['Refresh'] = $handler->RedirectTime . ' URL=' . $handler->RedirectLocation;
                    }
                    else
                    {
                        $FinalHeaders['Location'] = $handler->RedirectLocation;
                    }

                    $handler->setResponseCode(301);
                }
            }

            // Finally, set the response code
            http_response_code($handler->getResponseCode());

            if($handler->ResourceSource == ResourceSource::Page)
                Localization::setCookie();

            // Process and overwrite any headers set by the web application's configuration file
            $FinalHeaders = array_merge($FinalHeaders, $WebAppConfiguration->Headers);


            // Return all the headers
            foreach($FinalHeaders as $header => $value)
                header("$header: $value");

            foreach($handler->CookiesToSet as $cookie)
                setcookie($cookie->Name, $cookie->Value, $cookie->ExpiryTime, $cookie->Path, $cookie->Domain, $cookie->SecureOnly, $cookie->HttpOnly);
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

        /**
         * Converts an exception to an array representation
         *
         * @param Exception $e
         * @return array
         */
        public static function exceptionToArray(Exception $e): array
        {
            $return_results = [
                'file_path' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];

            if($e->getPrevious() !== null)
            {
                /** @noinspection PhpParamsInspection */
                $return_results['previous'] = self::exceptionToArray($e->getPrevious());
            }

            return $return_results;
        }
    }