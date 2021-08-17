<?php


namespace DynamicalWeb;


    use Exception;

    /**
     * Class CSS
     * @package DynamicalWeb
     */
    class CSS
    {
        /**
         * Loads an existing Javascript resource
         *
         * @param string $resource_name
         * @param bool Deprecated $minified
         * @throws Exception
         * @noinspection DuplicatedCode
         */
        public static function loadResource(string $resource_name)
        {
            $CssDirectory = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'css';

            if(file_exists($CssDirectory) == false)
            {
                throw new Exception('The directory "css" was not found in resources');
            }

            if(file_exists($CssDirectory . DIRECTORY_SEPARATOR . $resource_name . '.css.php') == false)
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
            include($CssDirectory . DIRECTORY_SEPARATOR . $resource_name . '.css.php');
            $Contents = ob_get_clean();
            header('Content-Type: text/css');

            $configuration = DynamicalWeb::getWebConfiguration();
            if(isset($configuration["configuration"]["compression"]["compress_css"]))
            {
                if((bool)$configuration["configuration"]["compression"]["compress_css"])
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
            $url = DynamicalWeb::$router->generate('resources_css', array('resource' => $resource_name));

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
        public static function importStyle(string $resource_name, array $parameters = array()): string
        {
            $Route = self::getResourceRoute($resource_name, $parameters);
            $Output = "<link href=\"$Route\" rel=\"stylesheet\">";
            HTML::print($Output, false);
            return $Output;
        }

        /**
         * Minfies the CSS code
         *
         * @param string $input
         * @return string
         */
        public static function minify(string $input): string
        {
            if(trim($input) === "") return $input;
            return preg_replace(
                array(
                    // Remove comment(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                    // Remove unused white-space(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                    // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                    '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                    // Replace `:0 0 0 0` with `:0`
                    '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                    // Replace `background-position:0` with `background-position:0 0`
                    '#(background-position):0(?=[;\}])#si',
                    // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                    '#(?<=[\s:,\-])0+\.(\d+)#s',
                    // Minify string value
                    '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                    '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                    // Minify HEX color code
                    '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                    // Replace `(border|outline):none` with `(border|outline):0`
                    '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                    // Remove empty selector(s)
                    '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
                ),
                array(
                    '$1',
                    '$1$2$3$4$5$6$7',
                    '$1',
                    ':0',
                    '$1:0 0',
                    '.$1',
                    '$1$3',
                    '$1$2$4$5',
                    '$1$2$3',
                    '$1:0',
                    '$1$2'
                ),
                $input);
        }
    }