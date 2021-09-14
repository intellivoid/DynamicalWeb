<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    use DynamicalWeb\Abstracts\RequestMethod;

    class Route
    {
        /**
         * The Request Methods allowed for this route
         *
         * @var string[]|RequestMethod[]
         */
        public $RequestMethods;

        /**
         * The URI path that this route handles
         *
         * @var string
         */
        public $Path;

        /**
         * The page that this route is pointing to
         *
         * @var string
         */
        public $Page;

        /**
         * The inline parameters present in the request path eg; /tests/error_code/%s => 'code'
         * /tests/error_code/404
         * Request::getDefinedDynamicParameters()['code']
         *
         * @var string[]
         */
        public $InlineParameters;

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'method' => $this->RequestMethods,
                'path' => $this->Path,
                'page' => $this->Page,
                'params' => $this->InlineParameters
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return Route
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): Route
        {
            $RouteObject = new Route();

            if(isset($data['method']))
                $RouteObject->RequestMethods = $data['method'];

            if(isset($data['path']))
                $RouteObject->Path = $data['path'];

            if(isset($data['page']))
                $RouteObject->Page = $data['page'];

            if(isset($data['params']))
                $RouteObject->InlineParameters = $data['params'];

            return $RouteObject;
        }
    }