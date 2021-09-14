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
         * Indicates if the framework signature should be included in the request or not
         *
         * @var bool
         */
        public $FrameworkSignature;

        public function __construct()
        {
            $this->PrimaryLanguage = 'en';
            $this->LocalizationEnabled = false;
            $this->DebuggingMode = true;
            $this->FrameworkSignature = true;
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
                'localization_enabled' => $this->LocalizationEnabled,
                'debugging_mode' => $this->DebuggingMode,
                'framework_signature' => $this->FrameworkSignature
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

            if(isset($data['localization_enabled']))
                $configurationObject->LocalizationEnabled = $data['localization_enabled'];

            if(isset($data['debugging_mode']))
                $configurationObject->DebuggingMode = $data['debugging_mode'];

            if(isset($data['framework_signature']))
                $configurationObject->FrameworkSignature = $data['framework_signature'];

            return $configurationObject;
        }
    }