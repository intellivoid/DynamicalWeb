<?php


    namespace DynamicalWeb;

    use Exception;

    /**
     * Class Runtime
     * @package DynamicalWeb
     */
    class Runtime
    {
        /**
         * Executes a runtime script
         *
         * @param string $script_name
         * @throws Exception
         */
        public static function executeScript(string $script_name)
        {
            $script_path = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'runtime_scripts' . DIRECTORY_SEPARATOR . $script_name;

            if(file_exists($script_path) == false)
            {
                throw new Exception('The runtime script ' . $script_name . ' cannot be found');
            }

            /** @noinspection PhpIncludeInspection */
            include($script_path);
        }

        /**
         * @param string $event
         * @throws Exception
         */
        public static function runEventScripts(string $event)
        {
            $configuration = DynamicalWeb::getWebConfiguration();
            foreach($configuration['runtime_scripts'][$event] as $script)
            {
                self::executeScript($script);
            }
        }

        /**
         * Imports a library
         *
         * @param string $library_name
         * @throws Exception
         */
        public static function import(string $library_name)
        {
            if(isset(DynamicalWeb::$loadedLibraries[$library_name]) == true)
            {
                return;
            }

            $configuration =  DynamicalWeb::getWebConfiguration();

            if(isset($configuration['libraries'][$library_name]) == false)
            {
                throw new Exception('The library "' . $library_name . '" was not found in WebConfiguration');
            }

            $LibrariesPath = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;
            $LibraryPath = $LibrariesPath . $configuration['libraries'][$library_name]['directory_name'];
            $AutoloaderPath = $LibraryPath . DIRECTORY_SEPARATOR . $configuration['libraries'][$library_name]['autoloader'];

            if(file_exists($LibrariesPath) == false)
            {
                throw new Exception('The resources directory for libraries was not found');
            }

            if(file_exists($LibraryPath) == false)
            {
                throw new Exception('The directory for the library "' . $library_name . '" was not found');
            }

            if(file_exists($AutoloaderPath) == false)
            {
                throw new Exception('The autoloader for the library "' . $library_name . '" was not found');
            }

            if($configuration['libraries'][$library_name]['check_class_exists'] == true)
            {
                $Namespace = $configuration['libraries'][$library_name]['namespace'];
                $ClassName = $configuration['libraries'][$library_name]['main_class'];

                if(class_exists($Namespace . "\\" . $ClassName) == true)
                {
                    return;
                }
            }

            /** @noinspection PhpIncludeInspection */
            include_once($AutoloaderPath);
            DynamicalWeb::$loadedLibraries[] = $library_name;
            self::runEventScripts('on_import');
        }
    }