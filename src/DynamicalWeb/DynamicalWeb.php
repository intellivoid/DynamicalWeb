<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb;

    use acm2\acm2;
    use acm2\Exceptions\ConfigurationNotDefinedException;
    use acm2\Objects\Schema;
    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Classes\Localization;
    use DynamicalWeb\Classes\Request;
    use DynamicalWeb\Classes\Router;
    use DynamicalWeb\Classes\WebApplication;
    use DynamicalWeb\Classes\WebAssets;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\RequestHandler;
    use DynamicalWeb\Objects\WebApplication\RuntimeScript;
    use Exception;
    use HttpStream\Exceptions\OpenStreamException;
    use HttpStream\Exceptions\RequestRangeNotSatisfiableException;
    use HttpStream\Exceptions\UnsupportedStreamException;
    use ppm\Exceptions\AutoloaderException;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\ppm;

    /**
     * DynamicalWeb Library
     *
     * Class DynamicalWeb
     * @package DynamicalWeb
     */
    class DynamicalWeb
    {
        /**
         * An array of objects that are temporarily stored in memory
         *
         * @var array
         */
        public static $globalObjects = [];

        /**
         * @var acm2
         */
        private static $acm;

        /**
         * Constructs a request handler with all the pre-defined properties
         *
         * @return RequestHandler
         * @throws WebApplicationException
         */
        public static function constructRequestHandler(): RequestHandler
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('DynamicalWeb::constructRequestHandler() can only execute if the Web Application is initialized');

            $request_handler = new RequestHandler();
            $request_handler->RequestMethod = Request::getRequestMethod();
            $request_handler->GetParameters = Request::getGetParameters();
            $request_handler->PostParameters = Request::getPostParameters();
            $request_handler->DynamicParameters = Request::getDefinedDynamicParameters();
            $request_handler->Parameters = Request::getParameters();
            $request_handler->PostBody = Request::getPostBody();
            $request_handler->Cookies = $_COOKIE;

            DynamicalWeb::activeRequestHandler($request_handler);
            Localization::setCookie();

            return DynamicalWeb::activeRequestHandler();
        }

        /**
         * Handles the request but doesn't complete the request
         *
         * @param string|null $requestUrl
         * @param string|null $requestMethod
         * @return RequestHandler
         * @noinspection DuplicatedCode
         */
        public static function getRequestHandler(?string $requestUrl=null, string $requestMethod = null): RequestHandler
        {
            /** @var Router $router */
            $router = DynamicalWeb::getMemoryObject('app_router');
            $match = $router->match($requestUrl, $requestMethod);
            $request_handler = new RequestHandler();

            // call closure or throw 404 status
            if(is_array($match) && is_callable($match['target']))
            {
                try
                {
                    $request_handler = call_user_func_array($match['target'], array_values($match['params']));
                }
                catch(Exception $e)
                {
                    $request_handler->ResourceSource = ResourceSource::Page;
                    $request_handler->Source = '500';
                    $request_handler->ResponseCode = 500;
                    $request_handler->ResponseContentType = BuiltinMimes::Html;
                }
            }
            else
            {
                $request_handler->ResourceSource = ResourceSource::Page;
                $request_handler->Source = '404';
                $request_handler->ResponseCode = 404;
                $request_handler->ResponseContentType = BuiltinMimes::Html;
            }

            DynamicalWeb::setMemoryObject('request_handler', $request_handler);
            return $request_handler;
        }

        /**
         * Returns an array of pre-defined definitions created by DynamicalWeb
         *
         * @return array
         * @throws WebApplicationException
         */
        public static function getDefinitions(): array
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('You cannot get DynamicalWeb definitions if no web application has been initialized');

            return [
                'DYNAMICAL_INITIALIZED' => (bool)self::getDefinition('DYNAMICAL_INITIALIZED'),
                'DYNAMICAL_APP_RESOURCES_PATH' => self::getDefinition('DYNAMICAL_APP_RESOURCES_PATH'),
                'DYNAMICAL_APP_CONFIGURATION_PATH' => self::getDefinition('DYNAMICAL_APP_CONFIGURATION_PATH'),
                'DYNAMICAL_APP_NAME' => self::getDefinition('DYNAMICAL_APP_NAME'),
                'DYNAMICAL_APP_NAME_SAFE' => self::getDefinition('DYNAMICAL_APP_NAME_SAFE'),
                'DYNAMICAL_APP_VERSION' => self::getDefinition('DYNAMICAL_APP_VERSION'),
                'DYNAMICAL_APP_AUTHOR' => self::getDefinition('DYNAMICAL_APP_AUTHOR'),
                'DYNAMICAL_APP_ORGANIZATION' => self::getDefinition('DYNAMICAL_APP_ORGANIZATION'),
                'DYNAMICAL_APP_DEBUGGING_MODE' => self::getDefinition('DYNAMICAL_APP_DEBUGGING_MODE'),
                'DYNAMICAL_FRAMEWORK_VERSION' => self::getDefinition('DYNAMICAL_FRAMEWORK_VERSION'),
                'DYNAMICAL_FRAMEWORK_AUTHOR' => self::getDefinition('DYNAMICAL_FRAMEWORK_AUTHOR'),
                'DYNAMICAL_FRAMEWORK_ORGANIZATION' => self::getDefinition('DYNAMICAL_FRAMEWORK_ORGANIZATION'),
                'DYNAMICAL_CLIENT_IP_ADDRESS' => self::getDefinition('DYNAMICAL_CLIENT_IP_ADDRESS'),
                'DYNAMICAL_CLIENT_USER_AGENT' => self::getDefinition('DYNAMICAL_CLIENT_USER_AGENT'),
                'DYNAMICAL_CLIENT_OS_FAMILY' => self::getDefinition('DYNAMICAL_CLIENT_OS_FAMILY'),
                'DYNAMICAL_CLIENT_OS_VERSION' => self::getDefinition('DYNAMICAL_CLIENT_OS_VERSION'),
                'DYNAMICAL_CLIENT_DEVICE_FAMILY' => self::getDefinition('DYNAMICAL_CLIENT_DEVICE_FAMILY'),
                'DYNAMICAL_CLIENT_DEVICE_BRAND' => self::getDefinition('DYNAMICAL_CLIENT_DEVICE_BRAND'),
                'DYNAMICAL_CLIENT_DEVICE_MODEL' => self::getDefinition('DYNAMICAL_CLIENT_DEVICE_MODEL'),
                'DYNAMICAL_CLIENT_FAMILY' => self::getDefinition('DYNAMICAL_CLIENT_FAMILY'),
                'DYNAMICAL_CLIENT_VERSION' => self::getDefinition('DYNAMICAL_CLIENT_VERSION'),
                'DYNAMICAL_CLIENT_IS_MOBILE_BROWSER' => (bool)self::getDefinition('DYNAMICAL_CLIENT_IS_MOBILE_BROWSER'),
                'DYNAMICAL_CLIENT_IS_MOBILE_DEVICE' => (bool)self::getDefinition('DYNAMICAL_CLIENT_IS_MOBILE_DEVICE'),
                'DYNAMICAL_CLIENT_IS_MOBILE' => (bool)self::getDefinition('DYNAMICAL_CLIENT_IS_MOBILE'),
                'DYNAMICAL_CLIENT_PREFERRED_PRIMARY_LOCALIZATION' => self::getDefinition('DYNAMICAL_CLIENT_PREFERRED_PRIMARY_LOCALIZATION'),
                'DYNAMICAL_CLIENT_PREFERRED_PRIMARY_ALT_LOCALIZATION' => self::getDefinition('DYNAMICAL_CLIENT_PREFERRED_PRIMARY_ALT_LOCALIZATION'),
                'DYNAMICAL_CLIENT_PREFERRED_SECONDARY_LOCALIZATION' => self::getDefinition('DYNAMICAL_CLIENT_PREFERRED_SECONDARY_LOCALIZATION'),
                'DYNAMICAL_CLIENT_PREFERRED_SECONDARY_ALT_LOCALIZATION' => self::getDefinition('DYNAMICAL_CLIENT_PREFERRED_SECONDARY_ALT_LOCALIZATION'),
                'DYNAMICAL_LOCALIZATION_ENABLED' => (bool)self::getDefinition('DYNAMICAL_LOCALIZATION_ENABLED'),
                'DYNAMICAL_LOCALIZATION_COOKIE' => self::getDefinition('DYNAMICAL_LOCALIZATION_COOKIE'),
                'DYNAMICAL_PRIMARY_LOCALIZATION' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION'),
                'DYNAMICAL_PRIMARY_LOCALIZATION_PATH' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION_PATH'),
                'DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE'),
                'DYNAMICAL_SELECTED_LOCALIZATION' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION'),
                'DYNAMICAL_SELECTED_LOCALIZATION_PATH' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION_PATH'),
                'DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE'),
                'DYNAMICAL_PAGES_PATH' => self::getDefinition('DYNAMICAL_PAGES_PATH'),
                'DYNAMICAL_HOME_PAGE' => self::getDefinition('DYNAMICAL_HOME_PAGE'),
                'DYNAMICAL_CURRENT_PAGE' => self::getDefinition('DYNAMICAL_CURRENT_PAGE'),
                'DYNAMICAL_CURRENT_PAGE_PATH' => self::getDefinition('DYNAMICAL_CURRENT_PAGE_PATH'),
                'DYNAMICAL_CURRENT_PAGE_EXECUTION_POINT' => self::getDefinition('DYNAMICAL_CURRENT_PAGE_EXECUTION_POINT'),
                'DYNAMICAL_CURRENT_PAGE_ROUTE_PATH' => self::getDefinition('DYNAMICAL_CURRENT_PAGE_ROUTE_PATH'),
            ];
        }

        /**
         * Returns an existing definition if defined.
         *
         * @param string $definition
         * @return string|null
         */
        public static function getDefinition(string $definition): ?string
        {
            if(defined($definition))
                return constant($definition);
            return null;
        }

        /**
         * Loads a PPM Web application and executes it
         *
         * @param string $package
         * @param bool $import_dependencies
         * @param bool $throw_error
         * @throws AutoloaderException
         * @throws Exceptions\DirectoryNotFoundException
         * @throws Exceptions\FileNotFoundException
         * @throws Exceptions\LocalizationException
         * @throws Exceptions\RequestHandlerException
         * @throws Exceptions\WebApplicationConfigurationException
         * @throws InvalidComponentException
         * @throws InvalidPackageLockException
         * @throws OpenStreamException
         * @throws PackageNotFoundException
         * @throws RequestRangeNotSatisfiableException
         * @throws RouterException
         * @throws UnsupportedStreamException
         * @throws VersionNotFoundException
         * @throws WebApplicationException
         */
        public static function exec(string $package, bool $import_dependencies=true, bool $throw_error=true)
        {
            $decoded = explode('==', $package);
            if($decoded[1] == 'latest')
                $decoded[1] = ppm::getPackageLock()->getPackage($decoded[0])->getLatestVersion();
            $path = ppm::getPackageLock()->getPackage($decoded[0])->getPackagePath($decoded[1]); // Find the package path
            ppm::import($decoded[0], $decoded[1], $import_dependencies, $throw_error); // Import dependencies
            $WebApplication = new WebApplication($path);

            $WebApplication->initialize();
            $request_handler = DynamicalWeb::getRequestHandler();
            $request_handler->execute();
        }

        /**
         * Returns an array of default headers created by the server
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public static function getServerHeaders(): array
        {
            return [
                'X-Powered-By' => 'DynamicalWeb/' . self::getDefinition('DYNAMICAL_FRAMEWORK_VERSION'),
                'X-Organization' => self::getDefinition('DYNAMICAL_FRAMEWORK_ORGANIZATION')
            ];
        }

        /**
         * Returns an array of optional security headers created by the server
         *
         * @return string[]
         */
        public static function getSecurityHeaders(): array
        {
            return [
                'X-XSS-Protection' => '1; mode=block',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'sameorigin',
                'Referrer-Policy' => 'no-referrer'
            ];
        }

        /**
         * Sets an object to memory, and returns the object that's stored in memory
         *
         * @param string $variable_name
         * @param $object
         * @return mixed
         * @noinspection PhpMissingReturnTypeInspection
         * @noinspection RedundantSuppression
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
         * @noinspection PhpMissingReturnTypeInspection
         * @noinspection RedundantSuppression
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
         * Sets or gets the active request handler, this is usually available during the execution process
         * of a request, allowing the request handler to be modified during runtime
         *
         * @param RequestHandler|null $requestHandler
         * @return RequestHandler
         * @throws WebApplicationException
         */
        public static function activeRequestHandler(?RequestHandler $requestHandler=null): RequestHandler
        {
            if($requestHandler !== null)
                self::setMemoryObject('app_request_handler', $requestHandler);
            $requestHandler = self::getMemoryObject('app_request_handler');
            if($requestHandler == null)
                self::setMemoryObject('app_request_handler', DynamicalWeb::constructRequestHandler());
            return self::getMemoryObject('app_request_handler');
        }

        /**
         * Records an execution event
         *
         * @param RuntimeScript $runtimeScript
         */
        public static function recordExecutionEvent(RuntimeScript $runtimeScript)
        {
            if(self::getMemoryObject('executed_runtime_scripts') == null)
                self::setMemoryObject('executed_runtime_scripts', []);

            $executed_scripts = self::getMemoryObject('executed_runtime_scripts');
            if(in_array($runtimeScript->ExecutionPoint, $executed_scripts) == false)
            {
                $executed_scripts[] = $runtimeScript->ExecutionPoint;
                self::setMemoryObject('executed_runtime_scripts', $executed_scripts);
            }
        }

        /**
         * @param string $page
         * @param array $parameters
         * @return string
         * @throws Exceptions\RouterException
         */
        public static function getRoute(string $page, array $parameters = []): string
        {
            /** @var Router $router */
            $router = DynamicalWeb::getMemoryObject('app_router');
            $url = $router->generate($page);

            if(count($parameters) > 0)
            {
                $url .= '?' . http_build_query($parameters);
            }

            return $url;
        }

        /**
         * Prints out the route from the getRoute function
         *
         * @param string $page
         * @param array $parameters
         * @return void
         * @throws RouterException
         */
        public static function printRoute(string $page, array $parameters=[])
        {
            Html::print(self::getRoute($page, $parameters), false);
        }

        /**
         * Fetches the asset route
         *
         * @param string $name
         * @param string $path
         * @param array $parameters
         * @return string
         * @throws RouterException
         * @noinspection PhpUnused
         */
        public static function getAssetRoute(string $name, string $path, array $parameters = []): string
        {
            /** @var WebAssets[] $web_assets */
            $web_assets = DynamicalWeb::getMemoryObject('app_web_assets');

            foreach($web_assets as $asset_path => $asset)
            {
                if($name == $asset->getName())
                {
                    $url = DYNAMICAL_APP_ROOT_PATH . $asset->getRoutePath() . $path;

                    if(count($parameters) > 0)
                    {
                        $url .= '?' . http_build_query($parameters);
                    }

                    return $url;
                }
            }

            throw new RouterException('Cannot find asset \'' . $name . '\'');
        }

        /**
         * Prints the output of the method getAssetRoute
         *
         * @param string $name
         * @param string $path
         * @param array $parameters
         * @return void
         * @throws RouterException
         */
        public static function printAssetRoute(string $name, string $path, array $parameters=[])
        {
            Html::print(self::getAssetRoute($name, $path, $parameters), false);
        }

        /**
         * Gets a configuration for DynamicalWeb
         *
         * @param string $configuration_name
         * @return array
         * @throws ConfigurationNotDefinedException
         */
        public static function getConfiguration(string $configuration_name): array
        {
            if(self::$acm == null)
            {
                self::$acm = new acm2('DynamicalWeb');

                $CookieStorageSchema = new Schema();
                $CookieStorageSchema->setName('CookieStorage');
                $CookieStorageSchema->setDefinition('Enabled', true);
                $CookieStorageSchema->setDefinition('Driver', 'mysql');
                $CookieStorageSchema->setDefinition('Host', '127.0.0.1');
                $CookieStorageSchema->setDefinition('Port', 3306);
                $CookieStorageSchema->setDefinition('AuthenticationEnabled', true);
                $CookieStorageSchema->setDefinition('Username', 'admin');
                $CookieStorageSchema->setDefinition('Password', 'admin');
                $CookieStorageSchema->setDefinition('Name', 'cookie_storage');
                self::$acm->defineSchema($CookieStorageSchema);

                self::$acm->updateConfiguration();
            }

            return self::$acm->getConfiguration($configuration_name);
        }

        /**
         * Tells DynamicalWeb that there's an uncaught exception
         *
         * @param Exception|null $e
         * @throws Exceptions\RequestHandlerException
         * @throws OpenStreamException
         * @throws RequestRangeNotSatisfiableException
         * @throws UnsupportedStreamException
         * @throws WebApplicationException
         */
        public static function handleException(?Exception $e=null)
        {
            DynamicalWeb::setMemoryObject('app_error', $e);
            $request_handler = DynamicalWeb::activeRequestHandler();
            $request_handler->ResourceSource = ResourceSource::Page;
            $request_handler->Source = '500';
            $request_handler->ResponseCode = 500;
            $request_handler->ResponseContentType = BuiltinMimes::Html;
            $request_handler->execute(false);
        }
    }
