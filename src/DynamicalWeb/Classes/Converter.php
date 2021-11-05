<?php

    namespace DynamicalWeb\Classes;

    class Converter
    {
        /**
         * Converts the given string to a safe name
         *
         * @param string $input
         * @return string
         * @noinspection PhpStrFunctionsInspection
         */
        public static function toSafeName(string $input): string
        {
            $input = preg_replace('/[^a-z0-9\s\-]/i', '_', $input);
            // Replace all spaces with hyphens
            $input = preg_replace('/\s/', '_', $input);
            // Replace multiple hyphens with a single hyphen
            $input = preg_replace('/\-\-+/', '_', $input);
            // Remove leading and trailing hyphens, and then lowercase the URL
            $input = strtolower(trim($input, '_'));

            if (strlen($input) > 80)
            {
                $input = substr($input, 0, 80);
                if (strpos(substr($input, -20), '_') !== false)
                    $input = substr($input, 0, strrpos($input, '_'));

            }

            return $input;
        }
    }