<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    class LocalizationConfiguration
    {
        /**
         * Indicates if localization is enabled or not
         *
         * @var bool
         */
        public $Enabled;

        /**
         * The primary language of the Web Application
         *
         * @var string|null
         */
        public $PirmaryLanguage;

        /**
         * Indicates if DynamicalWeb should automatically detect the client's preferred language
         *
         * @var bool
         */
        public $AutoDetectPreference;

        /**
         * The path of the localization data
         *
         * @var string|null
         */
        public $LocalizationPath;

        public function __construct()
        {
            $this->Enabled = false;
            $this->PirmaryLanguage = 'en';
            $this->AutoDetectPreference = false;
            $this->LocalizationPath = null;
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public function toArray(): array
        {
            return [
                'enabled' => $this->Enabled,
                'primary_language' => $this->PirmaryLanguage,
                'auto_detect_preference' => $this->AutoDetectPreference,
                'localization_path' => $this->LocalizationPath
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return LocalizationConfiguration
         * @noinspection PhpPureAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public static function fromArray(array $data): LocalizationConfiguration
        {
            $LocalizationConfigurationObject = new LocalizationConfiguration();

            if(isset($data['enabled']))
                $LocalizationConfigurationObject->Enabled = $data['enabled'];

            if(isset($data['primary_language']))
                $LocalizationConfigurationObject->PirmaryLanguage = $data['primary_language'];

            if(isset($data['auto_detect_preference']))
                $LocalizationConfigurationObject->AutoDetectPreference = $data['auto_detect_preference'];

            if(isset($data['localization_path']))
                $LocalizationConfigurationObject->LocalizationPath = $data['localization_path'];

            return $LocalizationConfigurationObject;

        }

    }