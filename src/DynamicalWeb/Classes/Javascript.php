<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Objects\WebApplication\Configuration;

    class Javascript
    {
        /**
         * @var string
         */
        private $JavascriptPath;

        /**
         * Indicates if this class is enabled or not
         *
         * @var bool
         */
        private $Enabled = false;

        /**
         * @param string $web_application_name
         * @param string $resources_path
         * @param Configuration $configuration
         */
        public function __construct(string $web_application_name, string $resources_path, Configuration $configuration)
        {
            $this->JavascriptPath = $resources_path . DIRECTORY_SEPARATOR . 'javascript';

            if(is_dir($this->JavascriptPath) == false)
                return;

            $this->Enabled = true;
        }

        /**
         * @return bool
         */
        public function isEnabled(): bool
        {
            return $this->Enabled;
        }
    }