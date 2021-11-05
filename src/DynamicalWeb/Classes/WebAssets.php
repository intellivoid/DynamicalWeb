<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\RouterException;
    use MimeLib\MimeLib;

    class WebAssets
    {
        /**
         * @var string
         */
        private $AssetsPath;

        /**
         * @var string
         */
        private $RoutePath;

        /**
         * @var string
         */
        private $Name;

        /**
         * @param string $assets_path
         * @param string $route_path
         */
        public function __construct(string $assets_path, string $name, string $route_path)
        {
            $this->AssetsPath = $assets_path;
            $this->Name = $name;
            $this->RoutePath = $route_path;
        }
        /**
         * Initializes the web asset and loads it into memory
         *
         * @param WebApplication $webApplication
         * @throws RouterException
         * @noinspection DuplicatedCode
         * @noinspection RedundantSuppression
         */
        public function initialize(WebApplication $webApplication)
        {
            $assetsPath = $this->AssetsPath;
            $webApplication->getRouter()->map('GET', $this->getRoutePath() . "[**:path]", function() use ($assetsPath)
            {

                $requested_path = $assetsPath . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath(Request::getDefinedDynamicParameters()['path']);
                $original_path = $assetsPath . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath(Request::getDefinedDynamicParameters()['path']);
                $alternative_path = $requested_path . '.dyn'; // Compiled asset

                $client_request = DynamicalWeb::constructRequestHandler();
                $client_request->ResourceSource = ResourceSource::WebAsset;
                $client_request->CacheResponse = true;
                $client_request->DetectMime = false;

                if(file_exists($requested_path) == false)
                {
                    if(file_exists($alternative_path) == false)
                    {
                        $client_request->ResourceSource = ResourceSource::Page;
                        $client_request->Source = '404';
                        $client_request->ResponseCode = 404;
                        $client_request->ResponseContentType = BuiltinMimes::Html;

                        return $client_request;
                    }

                    $client_request->ResourceSource = ResourceSource::CompiledWebAsset;
                    $client_request->CacheResponse = false;
                    $client_request->ResponseCode = 200;
                    $requested_path = $alternative_path;
                }

                $client_request->Source = $requested_path;

                /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection PHP 7.1+ Compatibility */
                switch(strtolower(pathinfo($original_path)['extension']))
                {
                    case 'css':
                        $client_request->ResponseContentType = BuiltinMimes::Css;
                        break;

                    case 'js':
                        $client_request->ResponseContentType = BuiltinMimes::Javascript;
                        break;

                    case 'ico':
                        $client_request->ResponseContentType = BuiltinMimes::Icon;
                        break;

                    default:
                        $client_request->ResponseContentType = MimeLib::detectFileType($requested_path)->getMime();
                        break;
                }

                return $client_request;

            }, $this->AssetsPath);
        }

        /**
         * @return string
         */
        public function getRoutePath(): string
        {
            return $this->RoutePath;
        }

        /**
         * @param string $RoutePath
         * @noinspection PhpUnused
         */
        public function setRoutePath(string $RoutePath): void
        {
            $this->RoutePath = $RoutePath;
        }

        /**
         * @return string
         */
        public function getAssetsPath(): string
        {
            return $this->AssetsPath;
        }

        /**
         * @param string $AssetsPath
         * @noinspection PhpUnused
         */
        public function setAssetsPath(string $AssetsPath): void
        {
            $this->AssetsPath = $AssetsPath;
        }

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->Name;
        }
    }