<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\Localization\LocalizationText;
    use DynamicalWeb\Objects\WebApplication;

    class Localization
    {
        /**
         * The name of the web application
         *
         * @var string
         */
        private $WebApplicationName;

        /**
         * The safe version of the web application name
         *
         * @var string
         */
        private $WebApplicationNameSafe;

        /**
         * The primary localization, the fallback language
         *
         * @var \DynamicalWeb\Objects\Localization
         */
        private $PrimaryLanguage;

        /**
         * The selected language, the current language preferred by the user.
         *
         * @var \DynamicalWeb\Objects\Localization
         */
        private $SelectedLanguage;

        /**
         * The path of the localization directory
         *
         * @var string
         */
        private $LocalizationPath;

        /**
         * Indicates if this class is enabled or not
         *
         * @var bool
         */
        private $Enabled = false;

        /**
         * @param string $web_application_name
         * @param string $resources_path
         * @param WebApplication\Configuration $configuration
         * @throws LocalizationException
         */
        public function __construct(string $web_application_name, string $resources_path, WebApplication\Configuration $configuration)
        {
            if($configuration->LocalizationEnabled == false)
                return;

            $this->LocalizationPath = $resources_path . DIRECTORY_SEPARATOR . 'localization';
            if(is_dir($this->LocalizationPath) == false)
                throw new LocalizationException('The localization directory does not exist in the resources path');

            $primary_localization = $this->LocalizationPath . DIRECTORY_SEPARATOR . $configuration->PrimaryLanguage . '.json';
            if(file_exists($primary_localization) == false)
                throw new LocalizationException('The primary localization file \'' . $configuration->PrimaryLanguage . '.json\' was not found');

            $this->WebApplicationName = $web_application_name;
            $this->WebApplicationNameSafe = Converter::toSafeName($web_application_name);
            $this->PrimaryLanguage = \DynamicalWeb\Objects\Localization::fromFile($primary_localization);
            $this->SelectedLanguage = $this->PrimaryLanguage;

            if(isset($_COOKIE['language_' . $this->WebApplicationNameSafe]))
            {
                $selected_language = strtolower(stripslashes($_COOKIE['language_' . $this->WebApplicationNameSafe]));
                $selected_localization = $this->LocalizationPath . DIRECTORY_SEPARATOR . $selected_language . '.json';

                if(file_exists($selected_language))
                {
                    $this->SelectedLanguage = \DynamicalWeb\Objects\Localization::fromFile($selected_localization);
                }
            }

            $this->Enabled = true;
        }

        /**
         * Initializes the global variables for the web application localization
         *
         * @param Router $router
         * @throws WebApplicationException
         * @throws RouterException
         * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
         */
        public function initialize(Router &$router)
        {
            if(defined('DYNAMICAL_INITIALIZED'))
                throw new WebApplicationException('Cannot initialize ' . $this->WebApplicationName . ', another web application is already initialized');

            if($this->Enabled == false)
            {
                define('DYNAMICAL_LOCALIZATION_ENABLED', false);
                define('DYNAMICAL_LOCALIZATION_PATH', null);
                define('DYNAMICAL_LOCALIZATION_COOKIE', null);
                define('DYNAMICAL_PRIMARY_LOCALIZATION', null);
                define('DYNAMICAL_PRIMARY_LOCALIZATION_PATH', null);
                define('DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE', null);
                define('DYNAMICAL_SELECTED_LOCALIZATION', null);
                define('DYNAMICAL_SELECTED_LOCALIZATION_PATH', null);
                define('DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE', null);

                return;
            }

            // Create the runtime definitions
            define('DYNAMICAL_LOCALIZATION_ENABLED', true);
            define('DYNAMICAL_LOCALIZATION_PATH', $this->LocalizationPath);
            define('DYNAMICAL_LOCALIZATION_COOKIE', 'language_' . $this->WebApplicationNameSafe);
            define('DYNAMICAL_PRIMARY_LOCALIZATION', $this->PrimaryLanguage->Language);
            define('DYNAMICAL_PRIMARY_LOCALIZATION_PATH', $this->PrimaryLanguage->SourcePath);
            define('DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE', $this->PrimaryLanguage->IsoCode);
            define('DYNAMICAL_SELECTED_LOCALIZATION', $this->SelectedLanguage->Language);
            define('DYNAMICAL_SELECTED_LOCALIZATION_PATH', $this->SelectedLanguage->SourcePath);
            define('DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE', $this->SelectedLanguage->IsoCode);

            // Set the global variables
            DynamicalWeb::setMemoryObject('app_localization_primary', $this->PrimaryLanguage);
            DynamicalWeb::setMemoryObject('app_localization_selected', $this->SelectedLanguage);

            setcookie('language_' . $this->WebApplicationNameSafe, $this->SelectedLanguage->Language);

            $router->map('GET|POST', '/change_language', function()
            {
                if(Request::getParameter('language') !== null)
                {
                    Localization::changeLanguage(Request::getParameter('language'), false);
                }
            }, 'change_language');
        }

        /**
         * Changes the current language set to the web application
         *
         * @param string $language
         * @param bool $throw_errors
         * @throws LocalizationException
         * @throws WebApplicationException
         */
        public static function changeLanguage(string $language, bool $throw_errors=true)
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
            {
                if ($throw_errors)
                    throw new WebApplicationException('Localization::changeLanguage() can only execute if the Web Application is initialized');

                return;
            }

            $selected_language = strtolower(stripslashes($language));
            $selected_localization = DYNAMICAL_LOCALIZATION_PATH . DIRECTORY_SEPARATOR . $selected_language . '.json';

            if(file_exists($selected_localization) == false)
            {
                if($throw_errors)
                    throw new LocalizationException('The requested localization \'' . $selected_language . '\' does not exist');
            }

            setcookie(DYNAMICAL_LOCALIZATION_COOKIE, $selected_language);
            Actions::redirect(DYNAMICAL_HOME_PAGE);
        }

        /**
         * Loads the localization for a specified section
         *
         * @param string $section
         * @param string $section_name
         * @param bool $throw_errors
         * @throws LocalizationException
         * @throws WebApplicationException
         */
        public static function loadLocalization(string $section, string $section_name, bool $throw_errors=true)
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
            {
                if ($throw_errors)
                    throw new WebApplicationException('Localization::changeLanguage() can only execute if the Web Application is initialized');

                return;
            }

            if(DYNAMICAL_LOCALIZATION_ENABLED == false)
                return;

            /** @var \DynamicalWeb\Objects\Localization $SelectedLanguage */
            $SelectedLanguage = DynamicalWeb::getMemoryObject('app_localization_selected');

            /** @var \DynamicalWeb\Objects\Localization $PrimaryLanguage */
            $PrimaryLanguage = DynamicalWeb::getMemoryObject('app_localization_primary');

            // Set the language definitions for the Selected Language first
            $UsePrimary = false;

            switch($section)
            {
                case LocalizationSection::Page:
                    $SelectedSection = $SelectedLanguage->PageLocalizations;
                    $PrimarySection = $PrimaryLanguage->PageLocalizations;
                    break;

                case LocalizationSection::Section:
                    $SelectedSection = $SelectedLanguage->SectionLocalizations;
                    $PrimarySection = $SelectedLanguage->SectionLocalizations;
                    break;

                default:
                    throw new LocalizationException('The given section \'' . $section . '\' is not a valid LocalizationSection');
            }

            if(isset($SelectedSection[$section_name]) == false)
            {
                if(isset($PrimarySection[$section_name]))
                {
                    $UsePrimary = true;
                }
                else
                {
                    if($throw_errors)
                        throw new LocalizationException('There is no localization for \'' . $section_name . '\' in primary or selected language (Section \'' . $section . '\'');

                    return;
                }
            }

            if($UsePrimary == false)
            {
                /** @var LocalizationText $localization */
                foreach($SelectedSection[$section_name] as $localization)
                {
                    $name = strtoupper(Converter::toSafeName($localization->Name));
                    if(defined('TEXT_' . $name) == false)
                        define('TEXT_' . $name, $localization->Text);
                }
            }

            if($SelectedLanguage->Language !== $PrimaryLanguage->Language)
            {
                foreach($PrimarySection[$section_name] as $localization)
                {
                    $name = strtoupper(Converter::toSafeName($localization->Name));
                    if(defined('TEXT_' . $name) == false)
                        define('TEXT_' . $name, $localization->Text);
                }
            }

        }

        /**
         * @return bool
         */
        public function isEnabled(): bool
        {
            return $this->Enabled;
        }
    }