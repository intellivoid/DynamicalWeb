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
         * Compresses the Javascript Source code and returns the compressed output
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
         * Loads an existing Javascript resource
         *
         * @param string $resource_name
         * @param bool Deprecated $minified
         * @throws Exception
         */
        public static function loadResource(string $resource_name)
        {
            $JavascriptDirectory = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'javascript';

            if(file_exists($JavascriptDirectory) == false)
            {
                throw new Exception('The directory "javascript" was not found in resources');
            }

            if(file_exists($JavascriptDirectory . DIRECTORY_SEPARATOR . $resource_name . '.js.php') == false)
            {
                Response::setResponseCode(404);
                Page::staticResponse(
                    "404 Not Found", "Compiled resource not found",
                    "The requested compiled resource was not found"
                );
                exit();
            }

            ob_start();
            /** @noinspection PhpIncludeInspection */
            include($JavascriptDirectory . DIRECTORY_SEPARATOR . $resource_name . '.js.php');
            $Contents = ob_get_clean();

            header('Content-Type: application/javascript');

            $configuration = DynamicalWeb::getWebConfiguration();
            if(isset($configuration["configuration"]["compression"]["compress_javascript"]))
            {
                if((bool)$configuration["configuration"]["compression"]["compress_javascript"])
                {
                    print(self::minify($Contents));
                    return;
                }
            }

            print($Contents);
        }

        /**
         * @param string $resource_name
         * @param array $parameters
         * @return string
         * @throws Exception
         */
        public static function getResourceRoute(string $resource_name, array $parameters = array()): string
        {
            $url = null;

            /** @noinspection PhpUnhandledExceptionInspection */
            $url = DynamicalWeb::$router->generate('resources_js', array('resource' => $resource_name));

            if(count($parameters) > 0)
            {
                $url .= '?' . http_build_query($parameters);
            }

            return $url;
        }

        /**
         * Prints HTML Script tag for importing the dynamic javascript file
         *
         * @param string $resource_name
         * @param array $parameters
         * @return string
         * @throws Exception
         */
        public static function importScript(string $resource_name, array $parameters = array()): string
        {
            $Route = self::getResourceRoute($resource_name, $parameters);
            $Output = "<script src=\"$Route\" ></script>";
            HTML::print($Output, false);
            return $Output;
        }
    }
