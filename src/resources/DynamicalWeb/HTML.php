<?php

    namespace DynamicalWeb;

    /**
     * Basic HTML Utilities for rendering
     *
     * Class HTML
     * @package DynamicalWeb
     */
    class HTML
    {
        /**
         * Prints HTML output
         *
         * @param string $output
         * @param bool $escape_html
         */
        public static function print(string $output, bool $escape_html = true)
        {
            if($escape_html == true)
            {
                $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
            }

            print($output);
        }
    }