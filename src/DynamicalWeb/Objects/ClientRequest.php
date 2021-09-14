<?php

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\RequestMethod;
    use ppm\Utilities\CLI;

    class ClientRequest
    {
        /**
         * The request method used by the client
         *
         * @var string|RequestMethod
         */
        public $RequestMethod;

        /**
         * The page that the client is requesting
         *
         * @var string
         */
        public $Page;

        /**
         * The request parameters given by the client
         *
         * @var array
         */
        public $Parameters;

        /**
         * The GET parameters
         *
         * @var array
         */
        public $GetParameters;

        /**
         * The POST parameters
         *
         * @var array
         */
        public $PostParameters;

        /**
         * The Dynamical Parameters
         *
         * @var array
         */
        public $DynamicParameters;

        /**
         * The post body
         *
         * @var string|null
         */
        public $PostBody;

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                'request_method' => $this->RequestMethod,
                'page' => $this->Page,
                'parameters' => $this->Parameters,
                'get_parameters' => $this->GetParameters,
                'post_parameters' => $this->PostParameters,
                'dynamic_parameters' => $this->DynamicParameters,
                'post_body' => $this->PostBody
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return ClientRequest
         */
        public static function fromArray(array $data): ClientRequest
        {
            $ClientRequestObject = new ClientRequest();

            if(isset($data['request_method']))
                $ClientRequestObject->RequestMethod = $data['request_method'];

            if(isset($data['page']))
                $ClientRequestObject->Page = $data['page'];

            if(isset($data['parameters']))
                $ClientRequestObject->Parameters = $data['parameters'];

            if(isset($data['get_parameters']))
                $ClientRequestObject->GetParameters = $data['get_parameters'];

            if(isset($data['post_parameters']))
                $ClientRequestObject->PostParameters = $data['post_parameters'];

            if(isset($data['dynamic_parameters']))
                $ClientRequestObject->DynamicParameters = $data['dynamic_parameters'];

            if(isset($data['post_body']))
                $ClientRequestObject->PostBody = $data['post_body'];

            return $ClientRequestObject;
        }

        /**
         * @return RequestMethod|string
         */
        public function getRequestMethod(): RequestMethod|string
        {
            return $this->RequestMethod;
        }

        /**
         * @return string
         */
        public function getPage(): string
        {
            return $this->Page;
        }

        /**
         * @return array
         */
        public function getParameters(): array
        {
            return $this->Parameters;
        }

        /**
         * @return array
         */
        public function getGetParameters(): array
        {
            return $this->GetParameters;
        }

        /**
         * @return array
         */
        public function getPostParameters(): array
        {
            return $this->PostParameters;
        }

        /**
         * @return array
         */
        public function getDynamicParameters(): array
        {
            return $this->DynamicParameters;
        }

        /**
         * @return string|null
         */
        public function getPostBody(): ?string
        {
            return $this->PostBody;
        }
    }