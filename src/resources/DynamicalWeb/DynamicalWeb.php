<?php

    namespace DynamicalWeb;

    use Exception;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Actions.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Client.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'HTML.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Language.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'MarkdownParser.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Page.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Runtime.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities.php');

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
         * Defines the important variables for DynamicalWeb
         */
        public static function defineVariables()
        {
            $ClientIP = Client::getClientIP();
            if($ClientIP == "::1")
            {
                $ClientIP = "127.0.0.1";
            }

            define("CLIENT_REMOTE_HOST", $ClientIP);
            define("CLIENT_USER_AGENT", Client::getUserAgentRaw());

            try
            {
                $UserAgentParsed = Utilities::parse_user_agent(CLIENT_USER_AGENT);
            }
            catch(Exception $exception)
            {
                $UserAgentParsed = array();
            }

            if($UserAgentParsed['platform'])
            {
                define("CLIENT_PLATFORM", $UserAgentParsed['platform']);
            }
            else
            {
                define("CLIENT_PLATFORM", 'Unknown');
            }

            if($UserAgentParsed['browser'])
            {
                define("CLIENT_BROWSER", $UserAgentParsed['browser']);
            }
            else
            {
                define("CLIENT_BROWSER", 'Unknown');
            }

            if($UserAgentParsed['version'])
            {
                define("CLIENT_VERSION", $UserAgentParsed['version']);
            }
            else
            {
                define("CLIENT_VERSION", 'Unknown');
            }

            $ServerInformation = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'dynamicalweb.json');
            $ServerInformation = json_decode($ServerInformation, true);

            define("DYNAMICAL_WEB_AUTHOR", $ServerInformation['AUTHOR']);
            define("DYNAMICAL_WEB_COMPANY", $ServerInformation['COMPANY']);
            define("DYNAMICAL_WEB_VERSION", $ServerInformation['VERSION']);
        }

        /**
         * Returns a defined variable, returns null if it doesn't exist
         *
         * @param string $var
         * @return mixed|null
         */
        public static function getDefinedVariable(string $var)
        {
            if(defined($var))
            {
                return constant($var);
            }

            return null;
        }

        /**
         * Returns an array of "system" defined variables created by DynamicalWeb
         *
         * @return array
         */
        public static function getDefinedVariables()
        {
            return array(
                'DYNAMICAL_WEB_AUTHOR' => self::getDefinedVariable('DYNAMICAL_WEB_AUTHOR'),
                'DYNAMICAL_WEB_COMPANY' => self::getDefinedVariable('DYNAMICAL_WEB_COMPANY'),
                'DYNAMICAL_WEB_VERSION' => self::getDefinedVariable('DYNAMICAL_WEB_VERSION'),
                'CLIENT_REMOTE_HOST' => self::getDefinedVariable('CLIENT_REMOTE_HOST'),
                'CLIENT_USER_AGENT' => self::getDefinedVariable('CLIENT_USER_AGENT'),
                'CLIENT_PLATFORM' => self::getDefinedVariable('CLIENT_PLATFORM'),
                'CLIENT_BROWSER' => self::getDefinedVariable('CLIENT_BROWSER'),
                'CLIENT_VERSION' => self::getDefinedVariable('CLIENT_VERSION'),
                'APP_HOME_PAGE' => self::getDefinedVariable('APP_HOME_PAGE'),
                'APP_PRIMARY_LANGUAGE' => self::getDefinedVariable('APP_PRIMARY_LANGUAGE'),
                'APP_RESOURCES_DIRECTORY' => self::getDefinedVariable('APP_RESOURCES_DIRECTORY'),
                'APP_CURRENT_PAGE' => self::getDefinedVariable('APP_CURRENT_PAGE'),
                'APP_CURRENT_PAGE_DIRECTORY' => self::getDefinedVariable('APP_CURRENT_PAGE_DIRECTORY'),
                'APP_SELECTED_LANGUAGE' => self::getDefinedVariable('APP_SELECTED_LANGUAGE'),
                'APP_SELECTED_LANGUAGE_FILE' => self::getDefinedVariable('APP_SELECTED_LANGUAGE_FILE'),
                'APP_FALLBACK_LANGUAGE_FILE' => self::getDefinedVariable('APP_FALLBACK_LANGUAGE_FILE'),
                'APP_LANGUAGE_ISO_639' => self::getDefinedVariable('APP_LANGUAGE_ISO_639')
            );
        }

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