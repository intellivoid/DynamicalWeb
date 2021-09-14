<?php

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\ResponseContentSource;

    class ServerResponse
    {
        /**
         * The response code for the response
         *
         * @var int
         */
        public $ResponseCode;

        /**
         * @var int
         */
        public $ContentType;

        /**
         * @var array
         */
        public $ResponseHeaders;

        /**
         * @var int
         */
        public $ContentSize;

        /**
         * The content source to retrieve the content from
         *
         * @var string|ResponseContentSource
         */
        public $ContentSource;

        /**
         * The content iself, depending on the ContentSource, the framework will use this value as a resource.
         *
         * @var string
         */
        public $Content;

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                'response_code' => $this->ResponseCode,
                'content_type' => $this->ContentType,
                'response_headers' => $this->ResponseHeaders,
                'content_size' => $this->ContentSize,
                'content_source' => $this->ContentSource,
                'content' => $this->Content
            ];
        }

        /**
         * Constructs an object from an array representation
         *
         * @param array $data
         * @return ServerResponse
         */
        public static function fromArray(array $data): ServerResponse
        {
            $ServerResponseObject = new ServerResponse();

            if(isset($data['response_code']))
                $ServerResponseObject->ResponseCode = $data['response_code'];

            if(isset($data['content_type']))
                $ServerResponseObject->ContentType = $data['content_type'];

            if(isset($data['response_headers']))
                $ServerResponseObject->ResponseHeaders = $data['response_headers'];

            if(isset($data['content_size']))
                $ServerResponseObject->ContentSize = $data['content_size'];

            if(isset($data['content_source']))
                $ServerResponseObject->ContentSource = $data['content_source'];

            if(isset($data['content']))
                $ServerResponseObject->Content = $data['content'];

            return $ServerResponseObject;
        }
    }