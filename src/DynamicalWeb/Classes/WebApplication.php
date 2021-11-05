<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Abstracts\WebAssetType;
    use DynamicalWeb\Classes\UserAgentParser\Parser;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\DirectoryNotFoundException;
    use DynamicalWeb\Exceptions\FileNotFoundException;
    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationConfigurationException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Exceptions\WebAssetsConfigurationException;
    use DynamicalWeb\Objects\WebApplication\Configuration;
    use DynamicalWeb\Objects\WebApplication\Route;
    use DynamicalWeb\Objects\WebApplication\WebAssetConfiguration;
    use ppm\Exceptions\AutoloaderException;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\ppm;

    class WebApplication
    {
        /**
         * The name of the web application
         *
         * @var string
         */
        public $Name;

        /**
         * The safe name presentation of the web application
         *
         * @var string
         */
        public $NameSafe;

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
         * @var WebAssets[]
         */
        private $WebAssets;

        /**
         * @var string|null
         */
        private $Version;

        /**
         * @var string|null
         */
        private $Author;

        /**
         * @var string|null
         */
        private $Organization;

        /**
         * The path for the Favicon file
         *
         * @var string
         */
        private $FaviconPath;

        /**
         * @param string $resources_path
         * @throws AutoloaderException
         * @throws DirectoryNotFoundException
         * @throws FileNotFoundException
         * @throws InvalidComponentException
         * @throws InvalidPackageLockException
         * @throws LocalizationException
         * @throws PackageNotFoundException
         * @throws VersionNotFoundException
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
                $this->Name = (string)$DecodedConfiguration['name'];

            if(isset($DecodedConfiguration['version']))
                $this->Version = (string)$DecodedConfiguration['version'];

            if(isset($DecodedConfiguration['author']))
                $this->Author = (string)$DecodedConfiguration['author'];

            if(isset($DecodedConfiguration['organization']))
                $this->Organization = (string)$DecodedConfiguration['organization'];

            $this->FaviconPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'BuiltinAssets' . DIRECTORY_SEPARATOR . 'favicon.ico';

            if($this->Configuration->Favicon !== null)
            {
                if(file_exists($this->ResourcesPath . DIRECTORY_SEPARATOR . $this->Configuration->Favicon))
                {
                    $this->FaviconPath = $this->ResourcesPath . DIRECTORY_SEPARATOR . $this->Configuration->Favicon;
                }
                else
                {
                    throw new FileNotFoundException('The file \'' . $this->ResourcesPath . DIRECTORY_SEPARATOR . $this->Configuration->Favicon . '\' was not found');
                }
            }


            $this->Routes = [];
            foreach($DecodedConfiguration['router'] as $datum)
                $this->Routes[] = Route::fromArray($datum);

            $this->Router = new Router();
            $this->Localization = new Localization($this->Name, $this->ResourcesPath, $this->Configuration);
            $this->PageIndexes = new PageIndexes($this->Name, $this->ResourcesPath, $this->Routes);
            $this->WebAssets = [];
            $this->NameSafe = Converter::toSafeName($this->Name);

            // Load the builtin DynamicalWeb Web Assets
            $this->loadLocalWebAsset(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'BuiltinAssets' . DIRECTORY_SEPARATOR . 'assets',  'dyn_builtin', 'dyn/assets');

            if(isset($DecodedConfiguration['web_assets']))
            {
                foreach($DecodedConfiguration['web_assets'] as $asset)
                {
                    $webAssetConfiguration = WebAssetConfiguration::fromArray($asset);
                    switch($webAssetConfiguration->Type)
                    {
                        case WebAssetType::Local:
                            $this->loadLocalWebAsset($this->ResourcesPath . DIRECTORY_SEPARATOR . $webAssetConfiguration->Source, $webAssetConfiguration->Name, $webAssetConfiguration->Path);
                            break;

                        case WebAssetType::PPM:
                            $this->loadPpmWebAsset($this->ResourcesPath . DIRECTORY_SEPARATOR . $webAssetConfiguration->Source, $webAssetConfiguration->Name, $webAssetConfiguration->Path);
                            break;
                    }
                }
            }
        }

        /**
         * Loads a local web asset
         *
         * @param string $path
         * @param string $name
         * @param string $route_path
         * @throws DirectoryNotFoundException
         */
        public function loadLocalWebAsset(string $path, string $name, string $route_path)
        {
            if(file_exists($path) == false || is_dir($path) == false)
                throw new DirectoryNotFoundException('The web assets directory \'' . $path . '\' was not found');
            $WebAsset = new WebAssets(realpath($path), $name, $route_path);
            $this->WebAssets[$WebAsset->getAssetsPath()] = $WebAsset;
        }

        /**
         * Loads a web asset from PPM
         *
         * @param string $package
         * @param string $name
         * @param string $route_path
         * @throws AutoloaderException
         * @throws DirectoryNotFoundException
         * @throws InvalidComponentException
         * @throws InvalidPackageLockException
         * @throws PackageNotFoundException
         * @throws VersionNotFoundException
         */
        public function loadPpmWebAsset(string $package, string $name, string $route_path)
        {
            $decoded = explode('==', $package);
            if($decoded[1] == 'latest')
                $decoded[1] = ppm::getPackageLock()->getPackage($decoded[0])->getLatestVersion();
            $path = ppm::getPackageLock()->getPackage($decoded[0])->getPackagePath($decoded[1]); // Find the package path
            ppm::import($decoded[0], $decoded[1]); // Import dependencies
            $this->loadLocalWebAsset($path, $name, $route_path); // Load it as a local web asset
        }

        /**
         * @throws RouterException
         * @throws WebApplicationException
         */
        public function initialize()
        {
            if(defined('DYNAMICAL_INITIALIZED'))
                throw new WebApplicationException('Cannot initialize ' . $this->Name . ', another web application is already initialized');

            define('DYNAMICAL_APP_ROOT_PATH', $this->Configuration->RootPath);

            // Initialize the localization engine
            $this->Localization->initialize($this->Router);

            // Initialize the page indexing engine
            $this->PageIndexes->initialize($this->Routes, $this->Router);

            // Detect and define the client (UserLand)
            $this->defineClientDefinitions();

            // Detect and define the framework (ServerLand)
            $this->defineFrameworkDefinitions();

            $favicon_path = $this->FaviconPath;
            /// Make favicon configurable
            $this->Router->map("GET", 'favicon.ico', function() use ($favicon_path)
            {
                $client_request = DynamicalWeb::constructRequestHandler();

                $client_request->ResourceSource = ResourceSource::WebAsset;
                $client_request->Source = $favicon_path;
                $client_request->ResponseCode = 200;
                $client_request->CacheResponse = true;
                $client_request->ResponseContentType = BuiltinMimes::Icon;

                return $client_request;
            }, 'favicon');

            foreach($this->WebAssets as $webAsset)
                $webAsset->initialize($this);

            // Define the router
            DynamicalWeb::setMemoryObject('app_router', $this->Router);
            DynamicalWeb::setMemoryObject('app_web_assets', $this->WebAssets);

            // Define the resources
            DynamicalWeb::setMemoryObject('app_configuration', $this->Configuration);
            define('DYNAMICAL_APP_RESOURCES_PATH', $this->ResourcesPath);
            define('DYNAMICAL_APP_CONFIGURATION_PATH', $this->ConfigurationFilePath);
            define('DYNAMICAL_APP_NAME', $this->Name);
            define('DYNAMICAL_APP_NAME_SAFE', $this->NameSafe);
            define('DYNAMICAL_APP_VERSION', $this->Version);
            define('DYNAMICAL_APP_AUTHOR', $this->Author);
            define('DYNAMICAL_APP_ORGANIZATION', $this->Organization);

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
         * @noinspection PhpCastIsUnnecessaryInspection
         */
        private function defineClientDefinitions()
        {
            $parser = new Parser();
            $parsed_ua = $parser->parse(Client::getUserAgentRaw());

            // Load mobile detection regex
            $MobileBrowserRegex = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'mobile1.regex');
            $MobileDeviceRegex = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'mobile2.regex');

            define('DYNAMICAL_CLIENT_IP_ADDRESS', Client::getClientIP());
            define('DYNAMICAL_CLIENT_USER_AGENT', $parsed_ua->originalUserAgent);
            define('DYNAMICAL_CLIENT_OS_FAMILY', ($parsed_ua->os->family == null ? null : $parsed_ua->os->family));
            define('DYNAMICAL_CLIENT_OS_VERSION', ($parsed_ua->os->toVersion() == null ? null : $parsed_ua->os->toVersion()));
            define('DYNAMICAL_CLIENT_DEVICE_FAMILY', ($parsed_ua->device->family == null ? null : $parsed_ua->device->family));
            define('DYNAMICAL_CLIENT_DEVICE_BRAND', ($parsed_ua->device->brand == null ? null : $parsed_ua->device->brand));
            define('DYNAMICAL_CLIENT_DEVICE_MODEL', ($parsed_ua->device->model == null ? null : $parsed_ua->device->model));
            define('DYNAMICAL_CLIENT_FAMILY', ($parsed_ua->ua->family == null ? null : $parsed_ua->ua->family));
            define('DYNAMICAL_CLIENT_VERSION', ($parsed_ua->ua->toVersion() == null ? null : $parsed_ua->ua->toVersion()));
            define('DYNAMICAL_CLIENT_IS_MOBILE_BROWSER', (bool)preg_match($MobileBrowserRegex, Client::getUserAgentRaw()));
            define('DYNAMICAL_CLIENT_IS_MOBILE_DEVICE', (bool)preg_match($MobileDeviceRegex, Client::getUserAgentRaw()));
            define('DYNAMICAL_CLIENT_IS_MOBILE', (bool)(DYNAMICAL_CLIENT_IS_MOBILE_BROWSER || DYNAMICAL_CLIENT_IS_MOBILE_DEVICE));
        }

        /**
         * Returns an array of default headers created by the server
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public static function getApplicationHeaders(): array
        {
            $WebApplicationString = DynamicalWeb::getDefinition('DYNAMICAL_APP_NAME');

            if(DynamicalWeb::getDefinition('DYNAMICAL_APP_VERSION') !== null)
                $WebApplicationString .= '/' . DynamicalWeb::getDefinition('DYNAMICAL_APP_VERSION');

            $ReturnHeaders = [
                'X-Application' => $WebApplicationString
            ];

            if(DynamicalWeb::getDefinition('DYNAMICAL_APP_AUTHOR') !== null)
                $ReturnHeaders['X-Application-Author'] = DynamicalWeb::getDefinition('DYNAMICAL_APP_AUTHOR');

            if(DynamicalWeb::getDefinition('DYNAMICAL_APP_ORGANIZATION') !== null)
                $ReturnHeaders['X-Application-Organization'] = DynamicalWeb::getDefinition('DYNAMICAL_APP_ORGANIZATION');

            return $ReturnHeaders;
        }

        /**
         * @return Configuration
         * @noinspection PhpUnused
         */
        public function getConfiguration(): Configuration
        {
            return $this->Configuration;
        }

        /**
         * @return Route[]
         * @noinspection PhpUnused
         */
        public function getRoutes(): array
        {
            return $this->Routes;
        }

        /**
         * @return PageIndexes
         * @noinspection PhpUnused
         */
        public function getPageIndexes(): PageIndexes
        {
            return $this->PageIndexes;
        }

        /**
         * @return Router
         */
        public function getRouter(): Router
        {
            return $this->Router;
        }

        /**
         * @return string|null
         */
        public function getVersion(): ?string
        {
            return $this->Version;
        }

        /**
         * @return string|null
         */
        public function getAuthor(): ?string
        {
            return $this->Author;
        }

        /**
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getOrganization(): ?string
        {
            return $this->Organization;
        }

        /**
         * @return WebAssets[]
         * @noinspection PhpUnused
         */
        public function getWebAssets(): array
        {
            return $this->WebAssets;
        }

        /**
         * @return string
         * @noinspection PhpUnused
         */
        public function getFaviconPath(): string
        {
            return $this->FaviconPath;
        }
    }