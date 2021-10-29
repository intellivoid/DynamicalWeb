<?php

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebAssetsConfigurationException;
    use DynamicalWeb\Objects\RequestHandler;
    use MimeLib\MimeLib;

    class WebAssets
    {
        /**
         * @var string
         */
        private string $WebAssetsPath;

        /**
         * @var string
         */
        private string $Name;

        /**
         * @var string
         */
        private string $ConfigurationFilePath;

        /**
         * @var \DynamicalWeb\Objects\WebAssets
         */
        private \DynamicalWeb\Objects\WebAssets $Configuration;

        /**
         * @var string
         */
        private string $AssetsPath;

        /**
         * @param string $assets_path
         * @throws WebAssetsConfigurationException
         */
        public function __construct(string $assets_path)
        {
            $this->Name = "Generic Assets";
            $this->WebAssetsPath = $assets_path;
            $this->ConfigurationFilePath = $this->WebAssetsPath . DIRECTORY_SEPARATOR . 'configuration.json';

            if(file_exists($this->ConfigurationFilePath) == false)
                throw new WebAssetsConfigurationException('The web assets configuration file \'configuration.json\' does not exist');

            // Parse the configuration file
            $DecodedConfiguration = json_decode(file_get_contents($this->ConfigurationFilePath), true);

            if($DecodedConfiguration == false)
                throw new WebAssetsConfigurationException('Cannot read web assets configuration file, ' . json_last_error_msg());

            if(isset($DecodedConfiguration['configuration']) == false)
                throw new WebAssetsConfigurationException('The main configuration is not set in the configuration file');

            $this->Configuration = \DynamicalWeb\Objects\WebAssets::fromArray($DecodedConfiguration);

            $this->AssetsPath = $this->WebAssetsPath . DIRECTORY_SEPARATOR . $this->Configuration->Configuration->Path;

            if(isset($DecodedConfiguration['name']))
                $this->Name = $DecodedConfiguration['name'];
        }

        /**
         * @return \DynamicalWeb\Objects\WebAssets
         */
        public function getConfiguration(): \DynamicalWeb\Objects\WebAssets
        {
            return $this->Configuration;
        }

        /**
         * Initializes the web asset and loads it into memory
         *
         * @param WebApplication $webApplication
         * @throws RouterException
         * @noinspection DuplicatedCode
         */
        public function initialize(WebApplication $webApplication)
        {
            DynamicalWeb::setMemoryObject('web_assets_' . $this->Name, $this->Configuration);

            $assetsPath = $this->AssetsPath;
            $webApplication->getRouter()->map('GET', $this->Configuration->Configuration->Route . "/[**:path]", function() use ($assetsPath)
            {

                $requested_path = $assetsPath . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath(Request::getDefinedDynamicParameters()['path']);
                $alternative_path = $requested_path . '.dyn'; // Compiled asset

                $client_request = DynamicalWeb::constructRequestHandler();
                $client_request->ResourceSource = ResourceSource::WebAsset;

                if(file_exists($requested_path) == false)
                {
                    if(file_exists($alternative_path) == false)
                    {
                        $client_request->ResourceSource = ResourceSource::Page;
                        $client_request->Source = '404';
                        $client_request->ResponseCode = 404;
                        $client_request->ResponseContentType = 'text/html';

                        return $client_request;
                    }

                    $client_request->ResourceSource = ResourceSource::CompiledWebAsset;
                    $client_request->ResponseCode = 200;
                    $requested_path = $alternative_path;
                }

                $client_request->Source = $requested_path;
                $client_request->ResponseContentType = MimeLib::detectFileType($requested_path);
                return $client_request;

            }, $this->Name);
        }

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->Name;
        }
    }