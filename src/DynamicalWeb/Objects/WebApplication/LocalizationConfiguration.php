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
        public $PirmaryLocalization;

        /**
         * Indicates if DynamicalWeb should automatically detect the client's preferred language
         *
         * @var bool
         */
        public $AutoDetectPreference;

        /**
         * An array of supported localizations and their paths
         *
         * @var array
         */
        public $Localizations;


        public function __construct()
        {
            $this->Enabled = false;
            $this->PirmaryLocalization = 'en';
            $this->AutoDetectPreference = true;
            $this->Localizations = [];
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
                'primary_language' => $this->PirmaryLocalization,
                'auto_detect_preference' => $this->AutoDetectPreference,
                'localizations' => $this->Localizations
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

            if(isset($data['primary_localization']))
                $LocalizationConfigurationObject->PirmaryLocalization = $data['primary_localization'];

            if(isset($data['auto_detect_preference']))
                $LocalizationConfigurationObject->AutoDetectPreference = $data['auto_detect_preference'];

            if(isset($data['localizations']))
                $LocalizationConfigurationObject->Localizations = $data['localizations'];

            return $LocalizationConfigurationObject;

        }

    }