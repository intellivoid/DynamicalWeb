<?php


    namespace DynamicalWeb;

    use Exception;
    use ppm\ppm;
    use RuntimeException;

    /**
     * Class Runtime
     * @package DynamicalWeb
     */
    class Runtime
    {
        /**
         * Indicates if PPM has been loaded
         *
         * @var bool
         */
        public static $PpmLoaded = false;

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

            if(self::$PpmLoaded == false)
            {
                if($configuration['enable_ppm'] == true)
                {
                    /** @noinspection PhpIncludeInspection */
                    require("ppm");

                    if(defined("PPM"))
                    {
                        self::$PpmLoaded = true;
                    }
                    else
                    {
                        throw new RuntimeException("Cannot start PPM, please make sure that PPM is installed properly.");
                    }
                }
            }

            if(isset($configuration['libraries'][$library_name]) == false)
            {
                throw new Exception('The library "' . $library_name . '" was not found in WebConfiguration');
            }

            if(isset($configuration['libraries'][$library_name]['package_name']))
            {
                // Import as a PPM package
                if($configuration['enable_ppm'] == false)
                {
                    throw new Exception('The library "' . $library_name . '" cannot be imported because it\'s a PPM package and PPM is not enabled in the configuration');
                }

                $version = 'latest';
                $import_dependencies = true;
                $throw_error = true;

                if(isset($configuration['libraries'][$library_name]['version']))
                {
                    $version = $configuration['libraries'][$library_name]['version'];
                }

                if(isset($configuration['libraries'][$library_name]['import_dependencies']))
                {
                    $import_dependencies = $configuration['libraries'][$library_name]['import_dependencies'];
                }

                if(isset($configuration['libraries'][$library_name]['throw_error']))
                {
                    $throw_error = $configuration['libraries'][$library_name]['throw_error'];
                }

                ppm::import($configuration['libraries'][$library_name]['package_name'], $version, $import_dependencies, $throw_error);
            }
            else
            {
                // Load from the libraries directory

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
            }

            DynamicalWeb::$loadedLibraries[] = $library_name;
            self::runEventScripts('on_import');
        }
    }