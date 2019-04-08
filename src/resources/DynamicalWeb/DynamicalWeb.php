<?php

    namespace DynamicalWeb;

    use Exception;

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
         * @throws Exception
         */
        public static function loadApplication(string $resourcesDirectory)
        {
            if(file_exists($resourcesDirectory . DIRECTORY_SEPARATOR . 'configuration.json') == false)
            {
                throw new Exception('The file "configuration.json" was not found in resources');
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
         * @throws Exception
         */
        public static function loadLibrary(string $libraryName, string $libraryDirectory, string $libraryLoader)
        {
            if(file_exists(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryDirectory) == false)
            {
                throw new Exception(sprintf("The requested library \"%s\" cannot be loaded because the directory was not found", $libraryName));
            }

            if(file_exists(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryDirectory . DIRECTORY_SEPARATOR . $libraryLoader) == false)
            {
                throw new Exception(sprintf("The requested library \"%s\" cannot be loaded because the loader was not found", $libraryName));
            }

            /** @noinspection PhpIncludeInspection */
            include_once(sprintf("%s%slibraries%s%s%s%s", APP_RESOURCES_DIRECTORY, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $libraryDirectory, DIRECTORY_SEPARATOR, $libraryLoader));
        }

        /**
         * Returns an existing configuration
         *
         * @param string $configuration_name
         * @return array
         * @throws Exception
         */
        public static function getConfiguration(string $configuration_name): array
        {
            $file = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'shared' . DIRECTORY_SEPARATOR . $configuration_name . '.json';

            if(file_exists($file) == false)
            {
                throw new Exception("The requested configuration '$configuration_name' does not exist in the shared resources folder");
            }

            return json_decode(file_get_contents($file), true);
        }
    }