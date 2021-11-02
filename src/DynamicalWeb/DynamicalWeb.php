<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb;

    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Classes\Localization;
    use DynamicalWeb\Classes\Request;
    use DynamicalWeb\Classes\Router;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\RequestHandler;
    use Exception;

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
         * Constructs a request handler with all the pre-defined properties
         *
         * @return RequestHandler
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
                catch(Exception)
                {
                    // TODO: Pass on error details
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
                'DYNAMICAL_LOCALIZATION_ENABLED' => self::getDefinition('DYNAMICAL_LOCALIZATION_ENABLED'),
                'DYNAMICAL_LOCALIZATION_PATH' => self::getDefinition('DYNAMICAL_LOCALIZATION_PATH'),
                'DYNAMICAL_LOCALIZATION_COOKIE' => self::getDefinition('DYNAMICAL_LOCALIZATION_COOKIE'),
                'DYNAMICAL_PRIMARY_LOCALIZATION' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION'),
                'DYNAMICAL_PRIMARY_LOCALIZATION_PATH' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION_PATH'),
                'DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE' => self::getDefinition('DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE'),
                'DYNAMICAL_SELECTED_LOCALIZATION' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION'),
                'DYNAMICAL_SELECTED_LOCALIZATION_PATH' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION_PATH'),
                'DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE' => self::getDefinition('DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE'),
                'DYNAMICAL_PAGES_PATH' => self::getDefinition('DYNAMICAL_PAGES_PATH'),
                'DYNAMICAL_HOME_PAGE' => self::getDefinition('DYNAMICAL_HOME_PAGE')
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
         * Returns an array of default headers created by the server
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
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

    }
