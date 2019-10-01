<?php


    namespace DynamicalWeb;


    use Exception;

    /**
     * Class Javascript
     * @package DynamicalWeb
     */
    class Javascript
    {
        /**
         * Compresses the Javascript Souce code and returns the compressed output
         *
         * @param string $src
         * @return string
         * @throws Exception
         */
        public static function minify(string $src): string
        {
            return JSMin::minify($src);
        }

        /**
         * @param string $resource_name
         * @param bool $minified
         * @throws Exception
         */
        public static function loadResource(string $resource_name, bool $minified)
        {
            $JavascriptDirectory = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'javascript';

            if(file_exists($JavascriptDirectory) == false)
            {
                throw new Exception('The directory "javascript" was not found in resources');
            }

            if(file_exists($JavascriptDirectory . DIRECTORY_SEPARATOR . $resource_name . '.js.php') == false)
            {
                http_response_code(404);
                Page::staticResponse(
                    "404 Not Found", "Compiled resource not found",
                    "The requests compiled resource was not found"
                );
            }

            ob_start();
            /** @noinspection PhpIncludeInspection */
            include($JavascriptDirectory . DIRECTORY_SEPARATOR . $resource_name . '.js.php');
            $Contents = ob_get_clean();

            if($minified)
            {
                print(self::minify($Contents));
            }
            else
            {
                print($Contents);
            }

        }
    }