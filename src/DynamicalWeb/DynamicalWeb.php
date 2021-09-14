<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb;

    use DynamicalWeb\Classes\Request;
    use DynamicalWeb\Classes\Router;
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

        public static function handleResponse(ServerResponse $serverResponse)
        {

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
