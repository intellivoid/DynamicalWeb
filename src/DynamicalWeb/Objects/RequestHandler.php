<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Abstracts\RequestMethod;
    use DynamicalWeb\Classes\PageIndexes;
    use DynamicalWeb\Classes\Utilities;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\RequestHandlerException;
    use Exception;
    use HttpStream\Exceptions\OpenStreamException;
    use HttpStream\Exceptions\RequestRangeNotSatisfiableException;
    use HttpStream\Exceptions\UnsupportedStreamException;
    use HttpStream\HttpStream;

    class RequestHandler
    {
        /**
         * The request method used by the client
         *
         * @var string|RequestMethod
         */
        public $RequestMethod;

        /**
         * @var string|ResourceSource
         */
        public $ResourceSource;

        /**
         * The source of the content the client is requesting or data itself
         *
         * @var string
         */
        public $Source;

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
         * An array of response headers
         *
         * @var array
         */
        public $ResponseHeaders;

        /**
         * The response code to return
         *
         * @var int
         */
        public $ResponseCode;

        /**
         * The content type of the response
         *
         * @var string
         */
        public $ResponseContentType;

        public function __construct()
        {
            $this->ResponseHeaders = [];
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                'request_method' => $this->RequestMethod,
                'requested_resource' => $this->ResourceSource,
                'source' => $this->Source,
                'parameters' => $this->Parameters,
                'get_parameters' => $this->GetParameters,
                'post_parameters' => $this->PostParameters,
                'dynamic_parameters' => $this->DynamicParameters,
                'post_body' => $this->PostBody,
                'response_headers' => $this->ResponseHeaders,
                'response_code' => $this->ResponseCode,
                'response_content_type' => $this->ResponseContentType
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return RequestHandler
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): RequestHandler
        {
            $ClientRequestObject = new RequestHandler();

            if(isset($data['request_method']))
                $ClientRequestObject->RequestMethod = $data['request_method'];

            if(isset($data['requested_resource']))
                $ClientRequestObject->ResourceSource = $data['requested_resource'];

            if(isset($data['source']))
                $ClientRequestObject->Source = $data['source'];

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

            if(isset($data['response_headers']))
                $ClientRequestObject->ResponseHeaders = $data['response_headers'];

            if(isset($data['response_code']))
                $ClientRequestObject->ResponseCode = $data['response_code'];

            if(isset($data['response_content_type']))
                $ClientRequestObject->ResponseContentType = $data['response_content_type'];

            return $ClientRequestObject;
        }

        /**
         * @return RequestMethod|string
         * @noinspection PhpUnused
         */
        public function getRequestMethod(): RequestMethod|string
        {
            return $this->RequestMethod;
        }

        /**
         * @return string
         */
        public function getSource(): string
        {
            return $this->Source;
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
         * @noinspection PhpUnused
         */
        public function getGetParameters(): array
        {
            return $this->GetParameters;
        }

        /**
         * @return array
         * @noinspection PhpUnused
         */
        public function getPostParameters(): array
        {
            return $this->PostParameters;
        }

        /**
         * @return array
         * @noinspection PhpUnused
         */
        public function getDynamicParameters(): array
        {
            return $this->DynamicParameters;
        }

        /**
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getPostBody(): ?string
        {
            return $this->PostBody;
        }

        /**
         * @return ResourceSource|string
         * @noinspection PhpUnused
         */
        public function getResourceSource(): string|ResourceSource
        {
            return $this->ResourceSource;
        }

        /**
         * @return array
         * @noinspection PhpUnused
         */
        public function getResponseHeaders(): array
        {
            return $this->ResponseHeaders;
        }

        /**
         * @param array $ResponseHeaders
         * @noinspection PhpUnused
         */
        public function setResponseHeaders(array $ResponseHeaders): void
        {
            $this->ResponseHeaders = $ResponseHeaders;
        }

        /**
         * @return int
         * @noinspection PhpUnused
         */
        public function getResponseCode(): int
        {
            return $this->ResponseCode;
        }

        /**
         * @param int $ResponseCode
         * @noinspection PhpUnused
         */
        public function setResponseCode(int $ResponseCode): void
        {
            $this->ResponseCode = $ResponseCode;
        }

        /**
         * @return string
         * @noinspection PhpUnused
         */
        public function getResponseContentType(): string
        {
            return $this->ResponseContentType;
        }

        /**
         * @param string $ResponseContentType
         * @noinspection PhpUnused
         */
        public function setResponseContentType(string $ResponseContentType): void
        {
            $this->ResponseContentType = $ResponseContentType;
        }

        /**
         * @throws UnsupportedStreamException
         * @throws RequestRangeNotSatisfiableException
         * @throws RequestHandlerException
         * @throws OpenStreamException
         */
        public function execute(bool $recursive=true)
        {
            /**
             * Finalizes the current state of the request handler, throws it into memory allowing any entity
             * that's current executing past this point to modify before it's finally executed.
             */
            DynamicalWeb::activeRequestHandler($this);

            switch($this->ResourceSource)
            {
                case ResourceSource::Memory:
                    Utilities::processHeaders(DynamicalWeb::activeRequestHandler());
                    Utilities::setContentSize(strlen($this->Source));
                    print($this->Source);
                    break;

                case ResourceSource::WebAsset:
                    Utilities::processHeaders(DynamicalWeb::activeRequestHandler());
                    HttpStream::streamToHttp($this->Source);
                    break;

                case ResourceSource::Page:
                    try
                    {
                        ob_start();
                        PageIndexes::load($this->Source);
                        $body_content = ob_get_clean();

                        Utilities::processHeaders(DynamicalWeb::activeRequestHandler());
                        Utilities::setContentSize(strlen($body_content));
                        print($body_content);
                    }
                    catch(Exception $e)
                    {
                        if($recursive)
                        {
                            $request_handler = DynamicalWeb::activeRequestHandler();
                            $request_handler->ResourceSource = ResourceSource::Page;
                            $request_handler->Source = '500';
                            $request_handler->ResponseCode = 500;
                            $request_handler->ResponseContentType = 'text/html';
                            $request_handler->execute(false);
                        }
                    }
                    break;

                default:
                    throw new RequestHandlerException('The resource source \'' . $this->ResourceSource . '\' cannot be processed');
            }
        }
    }