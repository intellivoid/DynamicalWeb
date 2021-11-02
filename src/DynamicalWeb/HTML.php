<?php

    namespace DynamicalWeb;

    class HTML
    {
        /**
         * Prints content out to be HTML friendly
         *
         * @param string $input
         * @param bool $escape
         * @param string $encoding
         */
        public static function print(string $input, bool $escape=true, string $encoding='UTF-8')
        {
            if($escape)
                $output = htmlspecialchars($input, ENT_QUOTES, $encoding);
            print($input);
        }
    }