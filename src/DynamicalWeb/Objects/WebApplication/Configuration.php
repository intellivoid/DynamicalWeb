<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    class Configuration
    {
        /**
         * The primary language of the web application
         *
         * @var string
         */
        public $PrimaryLanguage;

        /**
         * Indicates if localization is enabled
         *
         * @var bool
         */
        public $LocalizationEnabled;

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

        public function __construct()
        {
            $this->PrimaryLanguage = 'en';
            $this->Favicon = null;
            $this->LocalizationEnabled = false;
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
         */
        public function toArray(): array
        {
            return [
                'primary_language' => $this->PrimaryLanguage,
                'favicon' => $this->Favicon,
                'localization_enabled' => $this->LocalizationEnabled,
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
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): Configuration
        {
            $configurationObject = new Configuration();

            if(isset($data['primary_language']))
                $configurationObject->PrimaryLanguage = $data['primary_language'];

            if(isset($data['favicon']))
                $configurationObject->Favicon = $data['favicon'];

            if(isset($data['localization_enabled']))
                $configurationObject->LocalizationEnabled = $data['localization_enabled'];

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