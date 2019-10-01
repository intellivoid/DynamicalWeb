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
    }