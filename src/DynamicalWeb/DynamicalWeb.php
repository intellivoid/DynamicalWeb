<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb;

    use DynamicalWeb\Classes\Request;
    use DynamicalWeb\Classes\Router;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\ClientRequest;
    use DynamicalWeb\Objects\ServerResponse;

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
         * Handles the request but doesn't complete the request
         *
         * @param string|null $requestUrl
         * @param string|null $requestMethod
         * @return ClientRequest
         */
        public static function getClientRequest(?string $requestUrl=null, string $requestMethod = null): ClientRequest
        {
            /** @var Router $router */
            $router = DynamicalWeb::getMemoryObject('app_router');
            $match = $router->match($requestUrl, $requestMethod);

            // call closure or throw 404 status
            if(is_array($match) && is_callable($match['target']))
            {
                $client_request = call_user_func_array($match['target'], array_values($match['params']));
            }
            else
            {
                $client_request = new ClientRequest();
                $client_request->RequestMethod = Request::getRequestMethod();
                $client_request->GetParameters = Request::getGetParameters();
                $client_request->PostParameters = Request::getPostParameters();
                $client_request->DynamicParameters = Request::getDefinedDynamicParameters();
                $client_request->Parameters = Request::getParameters();
                $client_request->PostBody = Request::getPostBody();
                $client_request->Page = '404';
            }

            DynamicalWeb::setMemoryObject('client_request', $client_request);

            return $client_request;
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
        private static function getDefinition(string $definition): ?string
        {
            if(defined($definition))
                return constant($definition);
            return null;
        }

        public static function handleResponse(ServerResponse $serverResponse)
        {
            $request = self::getClientRequest();

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
    }
