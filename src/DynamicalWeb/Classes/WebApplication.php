<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Classes\UserAgentParser\Parser;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationConfigurationException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\WebApplication\Configuration;
    use DynamicalWeb\Objects\WebApplication\Route;

    class WebApplication
    {
        /**
         * The name of the web application
         *
         * @var string
         */
        public $Name;

        /**
         * The path of the resources' path for the web application
         *
         * @var string
         */
        public $ResourcesPath;

        /**
         * The configuration file path
         *
         * @var string
         */
        private $ConfigurationFilePath;

        /**
         * The configuration object of the web application
         *
         * @var Configuration
         */
        private $Configuration;

        /**
         * An array of defines routes for the web application
         *
         * @var Route[]
         */
        private $Routes;

        /**
         * The router processor object
         *
         * @var Router
         */
        private $Router;

        /**
         * The localization manager for the web application
         *
         * @var Localization
         */
        private $Localization;

        /**
         * @var PageIndexes
         */
        private $PageIndexes;

        /**
         * @param string $resources_path
         * @throws LocalizationException
         * @throws WebApplicationConfigurationException
         * @throws WebApplicationException
         */
        public function __construct(string $resources_path)
        {
            $this->Name = "DynamicalWeb Application";
            $this->ResourcesPath = $resources_path;
            $this->ConfigurationFilePath = $this->ResourcesPath . DIRECTORY_SEPARATOR . 'configuration.json';

            if(file_exists($this->ConfigurationFilePath) == false)
                throw new WebApplicationConfigurationException('The web application configuration file \'configuration.json\' does not exist');

            // Parse the configuration file
            $DecodedConfiguration = json_decode(file_get_contents($this->ConfigurationFilePath), true);

            if($DecodedConfiguration == false)
                throw new WebApplicationConfigurationException('Cannot read web application configuration file, ' . json_last_error_msg());

            if(isset($DecodedConfiguration['configuration']) == false)
                throw new WebApplicationConfigurationException('The main configuration is not set in the configuration file');

            $this->Configuration = Configuration::fromArray($DecodedConfiguration['configuration']);

            // Parse the route and initialize the router
            if(isset($DecodedConfiguration['router']) == false)
                throw new WebApplicationConfigurationException('The router configuration is not set in the configuration file');

            if(isset($DecodedConfiguration['name']))
                $this->Name = $DecodedConfiguration['name'];

            $this->Routes = [];
            foreach($DecodedConfiguration['router'] as $datum)
                $this->Routes[] = Route::fromArray($datum);

            $this->Router = new Router();
            $this->Localization = new Localization($this->Name, $this->ResourcesPath, $this->Configuration);
            $this->PageIndexes = new PageIndexes($this->Name, $this->ResourcesPath, $this->Routes);
        }

        /**
         * @throws RouterException
         * @throws WebApplicationException
         */
        public function initialize()
        {
            if(defined('DYNAMICAL_INITIALIZED'))
                throw new WebApplicationException('Cannot initialize ' . $this->Name . ', another web application is already initialized');

            // Initialize the localization engine
            $this->Localization->initialize($this->Router);

            // Initialize the page indexing engine
            $this->PageIndexes->initialize($this->Routes, $this->Router);

            // Detect and define the client (UserLand)
            $this->defineClientDefinitions();

            // Detect and define the framework (ServerLand)
            $this->defineFrameworkDefinitions();

            // Define the router
            DynamicalWeb::setMemoryObject('app_router', $this->Router);

            // Define the resources
            DynamicalWeb::setMemoryObject('app_configuration', $this->Configuration);
            define('DYNAMICAL_APP_RESOURCES_PATH', $this->ResourcesPath);
            define('DYNAMICAL_APP_CONFIGURATION_PATH', $this->ConfigurationFilePath);
            define('DYNAMICAL_APP_NAME', $this->Name);

            // Finally, define it as initialized
            define('DYNAMICAL_INITIALIZED', 1);
        }

        /**
         * Defines the framework definitions
         *
         * @throws WebApplicationException
         */
        private function defineFrameworkDefinitions()
        {
            $package_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'package.json';

            if(file_exists($package_path) == false)
                throw new WebApplicationException('Cannot find package.json in \'' . $package_path . '\'');

            $package_info = json_decode(file_get_contents($package_path), true);

            define('DYNAMICAL_FRAMEWORK_VERSION', $package_info['package']['version']);
            define('DYNAMICAL_FRAMEWORK_AUTHOR', $package_info['package']['author']);
            define('DYNAMICAL_FRAMEWORK_ORGANIZATION', $package_info['package']['organization']);
        }

        /**
         * Defines the client's definitions and parses the client user agent
         */
        private function defineClientDefinitions()
        {
            $parser = new Parser();
            $parsed_ua = $parser->parse(Client::getUserAgentRaw());

            define('DYNAMICAL_CLIENT_IP_ADDRESS', Client::getClientIP());
            define('DYNAMICAL_CLIENT_USER_AGENT', $parsed_ua->originalUserAgent);
            define('DYNAMICAL_CLIENT_OS_FAMILY', ($parsed_ua->os->family == null ? null : $parsed_ua->os->family));
            define('DYNAMICAL_CLIENT_OS_VERSION', ($parsed_ua->os->toVersion() == null ? null : $parsed_ua->os->toVersion()));
            define('DYNAMICAL_CLIENT_DEVICE_FAMILY', ($parsed_ua->device->family == null ? null : $parsed_ua->device->family));
            define('DYNAMICAL_CLIENT_DEVICE_BRAND', ($parsed_ua->device->brand == null ? null : $parsed_ua->device->brand));
            define('DYNAMICAL_CLIENT_DEVICE_MODEL', ($parsed_ua->device->model == null ? null : $parsed_ua->device->model));
            define('DYNAMICAL_CLIENT_FAMILY', ($parsed_ua->ua->family == null ? null : $parsed_ua->ua->family));
            define('DYNAMICAL_CLIENT_VERSION', ($parsed_ua->ua->toVersion() == null ? null : $parsed_ua->ua->toVersion()));
        }

        /**
         * @return Configuration
         */
        public function getConfiguration(): Configuration
        {
            return $this->Configuration;
        }

        /**
         * @return Route[]
         */
        public function getRoutes(): array
        {
            return $this->Routes;
        }

        /**
         * @return PageIndexes
         */
        public function getPageIndexes(): PageIndexes
        {
            return $this->PageIndexes;
        }
    }