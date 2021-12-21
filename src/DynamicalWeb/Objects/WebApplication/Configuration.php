<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    use khm\Abstracts\HostFlags;

    class Configuration
    {
        /**
         * The root path of the web application
         *
         * @var string
         */
        public $RootPath;

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

        /**
         * Indicates if KHM's firewall is enabled or not
         *
         * @var bool
         */
        public $KhmEnabled;

        /**
         * An array of flags to deny if detected by KHM
         *
         * @var string[]|HostFlags[]
         */
        public $FirewallDeny;

        /**
         * @noinspection PhpPureAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public function __construct()
        {
            $this->RootPath = '/';
            $this->Localization = new LocalizationConfiguration();
            $this->Favicon = null;
            $this->DebuggingMode = true;
            $this->FrameworkSignature = true;
            $this->ApplicationSignature = true;
            $this->SecurityHeaders = true;
            $this->KhmEnabled = false;
            $this->FirewallDeny = [];
            $this->Headers = [];
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         * @noinspection RedundantSuppression
         */
        public function toArray(): array
        {
            return [
                'root_path' => $this->RootPath,
                'localization' => $this->Localization->toArray(),
                'favicon' => $this->Favicon,
                'debugging_mode' => $this->DebuggingMode,
                'framework_signature' => $this->FrameworkSignature,
                'application_signature' => $this->ApplicationSignature,
                'security_headers' => $this->SecurityHeaders,
                'enable_khm' => $this->KhmEnabled,
                'firewall_deny' => $this->FirewallDeny,
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

            if(isset($data['root_path']))
                $configurationObject->RootPath = $data['root_path'];

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

            if(isset($data['enable_khm']))
                $configurationObject->KhmEnabled = (bool)$data['enable_khm'];

            if(isset($data['firewall_deny']))
                $configurationObject->FirewallDeny = $data['firewall_deny'];

            if(isset($data['headers']))
                $configurationObject->Headers = $data['headers'];

            return $configurationObject;
        }
    }