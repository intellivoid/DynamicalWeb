<?php

    namespace DynamicalWeb;

    use Exception;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'HTML.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Language.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'MarkdownParser.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Page.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Runtime.php');

    /**
     * Main DynamicalWeb Library
     *
     * Class DynamicalWeb
     * @package DynamicalWeb
     */
    class DynamicalWeb
    {
        /**
         * An array of already loaded libraries
         *
         * @var array
         */
        public static $loadedLibraries = [];

        /**
         * An array of objects that are temporarily stored in memory
         *
         * @var array
         */
        public static $globalObjects = [];

        /**
         * An array of variables that are temporarily stored in memory
         *
         * @var array
         */
        public static $globalVariables = [];

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
            Runtime::runEventScripts('initialize'); // Run events at initialize
        }

        /**
         * Gets the current web configuration
         *
         * @return array
         */
        public static function getWebConfiguration(): array
        {
            $ConfigurationFile = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'configuration.json';
            $Contents = file_get_contents($ConfigurationFile);
            return json_decode($Contents, true);
        }

        /**
         * Imports and loads a custom library server-sided
         *
         * This function will be removed in the future, use
         * Runtime instead to import runtime scripts/libraries
         *
         * @param string $libraryName
         * @param string $libraryDirectory
         * @param string $libraryLoader
         * @deprecated Use Runtime's import function instead
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

        /**
         * Sets an object to memory, and returns the object that's stored in memory
         *
         * @param string $variable_name
         * @param $object
         * @return mixed
         */
        public static function setMemoryObject(string $variable_name, $object)
        {
            DynamicalWeb::$globalObjects[$variable_name] = $object;
            return DynamicalWeb::$globalObjects[$variable_name];
        }

        /**
         * Gets an object from memory, if not set then it will return null
         *
         * @param string $variable_name
         * @return mixed|null
         */
        public static function getMemoryObject(string $variable_name)
        {
            if(isset(DynamicalWeb::$globalObjects[$variable_name]) == false)
            {
                return null;
            }

            return DynamicalWeb::$globalObjects[$variable_name];
        }

        /**
         * Sets a global string variable and returns the value from memory
         *
         * @param string $name
         * @param string $value
         * @return string
         */
        public static function setString(string $name, string $value): string
        {
            DynamicalWeb::$globalVariables['db 0x77'][$name] = $value;
            return DynamicalWeb::$globalVariables['db 0x77'][$name];
        }

        /**
         * Returns an existing global string variable
         *
         * @param string $name
         * @return string
         * @throws Exception
         */
        public static function getString(string $name): string
        {
            if(isset(DynamicalWeb::$globalVariables['db 0x77'][$name]) == false)
            {
                throw new Exception('"' . $name . '" is not defined in globalObjects[db 0x77]');
            }

            return DynamicalWeb::$globalVariables['db 0x77'][$name];
        }

        /**
         * Sets a global integer variable and returns the value from memory
         *
         * @param string $name
         * @param int $value
         * @return int
         */
        public static function setInt32(string $name, int $value): int
        {
            DynamicalWeb::$globalVariables['db 0x26'][$name] = $value;
            return DynamicalWeb::$globalVariables['db 0x26'][$name];
        }

        /**
         * returns an existing global integer variable
         *
         * @param string $name
         * @return int
         * @throws Exception
         */
        public static function getInt32(string $name): int
        {
            if(isset(DynamicalWeb::$globalVariables['db 0x26'][$name]) == false)
            {
                throw new Exception('"' . $name . '" is not defined in globalObjects[db 0x26]');
            }

            return DynamicalWeb::$globalVariables['db 0x26'][$name];
        }

        /**
         * Sets a global float variable and returns the value from memory
         *
         * @param string $name
         * @param float $value
         * @return float
         */
        public static function setFloat(string $name, float $value): float
        {
            DynamicalWeb::$globalVariables['db 0x29'][$name] = $value;
            return DynamicalWeb::$globalVariables['db 0x29'][$name];
        }

        /**
         * Returns an existing global float variable
         *
         * @param string $name
         * @return float
         * @throws Exception
         */
        public static function getFloat(string $name): float
        {
            if(isset(DynamicalWeb::$globalVariables['db 0x29'][$name]) == false)
            {
                throw new Exception('"' . $name . '" is not defined in globalObjects[db 0x29]');
            }

            return DynamicalWeb::$globalVariables['db 0x29'][$name];
        }

        /**
         * Sets a global boolean variable and returns the value from memory
         *
         * @param string $name
         * @param bool $value
         * @return bool
         */
        public static function setBoolean(string $name, bool $value): bool
        {
            DynamicalWeb::$globalVariables['db 0x43'][$name] = (int)$value;
            return (bool)DynamicalWeb::$globalVariables['db 0x43'][$name];
        }

        /**
         * Returns an existing global boolean variable
         *
         * @param string $name
         * @return bool
         * @throws Exception
         */
        public static function getBoolean(string $name): bool
        {
            if(isset(DynamicalWeb::$globalVariables['db 0x43'][$name]) == false)
            {
                throw new Exception('"' . $name . '" is not defined in globalObjects[db 0x43]');
            }

            return (bool)DynamicalWeb::$globalVariables['db 0x43'][$name];
        }

        /**
         * Sets a global array variable and returns the value from memory
         *
         * @param string $name
         * @param array $value
         * @return array
         */
        public static function setArray(string $name, array $value): array
        {
            DynamicalWeb::$globalVariables['db 0x83'][$name] = $value;
            return DynamicalWeb::$globalVariables['db 0x83'][$name];
        }

        /**
         * Returns an existing global array variable
         *
         * @param string $name
         * @return bool
         * @throws Exception
         */
        public static function getArray(string $name): array
        {
            if(isset(DynamicalWeb::$globalVariables['db 0x83'][$name]) == false)
            {
                throw new Exception('"' . $name . '" is not defined in globalObjects[db 0x83]');
            }

            return DynamicalWeb::$globalVariables['db 0x83'][$name];
        }
    }