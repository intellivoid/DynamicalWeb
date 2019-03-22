<?php

    namespace DynamicalWeb;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'HTML.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Language.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Page.php');

    /**
     * Main DynamicalWeb Library
     *
     * Class DynamicalWeb
     * @package DynamicalWeb
     */
    class DynamicalWeb
    {
        /**
         * Loads the application resources
         *
         * @param string $resourcesDirectory
         * @throws \Exception
         */
        public static function loadApplication(string $resourcesDirectory)
        {
            if(file_exists($resourcesDirectory . DIRECTORY_SEPARATOR . 'configuration.json') == false)
            {
                throw new \Exception('The file "configuration.json" was not found in resources');
            }

            $Configuration = json_decode(file_get_contents($resourcesDirectory . DIRECTORY_SEPARATOR . 'configuration.json'), true);

            define('APP_HOME_PAGE', $Configuration['home_page'], false);
            define('APP_PRIMARY_LANGUAGE', $Configuration['primary_language'], false);
            define('APP_RESOURCES_DIRECTORY', $resourcesDirectory, false);

            Language::loadLanguage();
        }

        /**
         * Imports and loads a custom library server-sided
         *
         * @param string $libraryName
         * @param string $libraryDirectory
         * @param string $libraryLoader
         * @throws \Exception
         */
        public static function loadLibrary(string $libraryName, string $libraryDirectory, string $libraryLoader)
        {
            if(file_exists(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryDirectory) == false)
            {
                throw new \Exception('The requested library "' . $libraryName . '" cannot be loaded because the directory was not found');
            }

            if(file_exists(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryDirectory . DIRECTORY_SEPARATOR . $libraryLoader) == false)
            {
                throw new \Exception('The requested library "' . $libraryName . '" cannot be loaded because the loader was not found');
            }

            include_once(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryDirectory . DIRECTORY_SEPARATOR . $libraryLoader);
        }
    }