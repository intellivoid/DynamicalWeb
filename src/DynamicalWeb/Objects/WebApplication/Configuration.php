<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    class Configuration
    {
        /**
         * The configuration for the localization
         *
         * @var LocalizationConfiguration
         */
        public $Localization;

        /**
         * Indicates if the web application is in debugging mode
         *
         * @var bool
         */
        public $DebuggingMode;

        /**
         * Indicates if the framework signature should be included in the response or not
         *
         * @var bool
         */
        public $FrameworkSignature;

        /**
         * Indicates if the web application signature should be included in the response or not
         *
         * @var bool
         */
        public $ApplicationSignature;

        /**
         * Indicates if the web server should return security headers
         *
         * @var bool
         */
        public $SecurityHeaders;

        /**
         * A list of hard-coded configured headers
         *
         * @var array
         */
        public $Headers;

        /**
         * @var string|null
         */
        public $Favicon;

        /** @noinspection PhpPureAttributeCanBeAddedInspection */
        public function __construct()
        {
            $this->Localization = new LocalizationConfiguration();
            $this->Favicon = null;
            $this->DebuggingMode = true;
            $this->FrameworkSignature = true;
            $this->ApplicationSignature = true;
            $this->SecurityHeaders = true;
            $this->Headers = [];
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'localization' => $this->Localization->toArray(),
                'favicon' => $this->Favicon,
                'debugging_mode' => $this->DebuggingMode,
                'framework_signature' => $this->FrameworkSignature,
                'application_signature' => $this->ApplicationSignature,
                'security_headers' => $this->SecurityHeaders,
                'headers' => $this->Headers
            ];
        }

        /**
         * Constructs the object from an array representation of the object
         *
         * @param array $data
         * @return Configuration
         */
        public static function fromArray(array $data): Configuration
        {
            $configurationObject = new Configuration();

            if(isset($data['localization']))
                $configurationObject->Localization = LocalizationConfiguration::fromArray($data['localization']);

            if(isset($data['favicon']))
                $configurationObject->Favicon = $data['favicon'];

            if(isset($data['debugging_mode']))
                $configurationObject->DebuggingMode = $data['debugging_mode'];

            if(isset($data['framework_signature']))
                $configurationObject->FrameworkSignature = $data['framework_signature'];

            if(isset($data['application_signature']))
                $configurationObject->ApplicationSignature = $data['application_signature'];

            if(isset($data['security_headers']))
                $configurationObject->SecurityHeaders = $data['security_headers'];

            if(isset($data['headers']))
                $configurationObject->Headers = $data['headers'];

            return $configurationObject;
        }
    }