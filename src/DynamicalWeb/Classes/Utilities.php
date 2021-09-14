<?php

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Exceptions\FileNotFoundException;

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
    }