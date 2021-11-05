<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Cookies;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\Cookie;
    use DynamicalWeb\Objects\LanguagePreference;
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
         * Indicates if this class is enabled or not
         *
         * @var bool
         */
        private $Enabled = false;

        /**
         * The resources path of the web application
         *
         * @var string
         */
        private $ResourcesPath;

        /**
         * The localization configuration
         *
         * @var WebApplication\LocalizationConfiguration
         */
        private $LocalizationConfiguration;

        /**
         * @param string $web_application_name
         * @param string $resources_path
         * @param WebApplication\Configuration $configuration
         * @throws LocalizationException
         */
        public function __construct(string $web_application_name, string $resources_path, WebApplication\Configuration $configuration)
        {
            if($configuration->Localization->Enabled == false)
                return;

            $this->ResourcesPath = $resources_path;
            $this->WebApplicationName = $web_application_name;
            $this->WebApplicationNameSafe = Converter::toSafeName($web_application_name);

            $primary_localization = $this->ResourcesPath . DIRECTORY_SEPARATOR . $configuration->Localization->Localizations[$configuration->Localization->PirmaryLocalization];
            if(file_exists($primary_localization) == false)
                throw new LocalizationException('The primary localization file \'' . $this->ResourcesPath . DIRECTORY_SEPARATOR . $configuration->Localization->Localizations[$configuration->Localization->PirmaryLocalization]. ' was not found');

            $this->PrimaryLanguage = \DynamicalWeb\Objects\Localization::fromFile($primary_localization);
            $this->SelectedLanguage = $this->PrimaryLanguage; // Set the selected as the primary, the primary will be the fallback.
            $this->LocalizationConfiguration = $configuration->Localization;

            // If the client is returning a set language
            if(isset($_COOKIE['language_' . $this->WebApplicationNameSafe]))
            {
                // Get saved preference
                $selected_language = strtolower(stripslashes($_COOKIE['language_' . $this->WebApplicationNameSafe]));
                $selected_localization = $this->ResourcesPath . DIRECTORY_SEPARATOR . $this->LocalizationConfiguration->Localizations[$selected_language];

                // Load the selected localization if it exists.
                if(file_exists($selected_localization))
                {
                    $this->SelectedLanguage = \DynamicalWeb\Objects\Localization::fromFile($selected_localization);
                }
            }
            else
            {
                // Or try to detect the client's preference
                foreach(self::detectPreferredClientLanguages() as $languagePreference)
                {
                    if(isset($this->LocalizationConfiguration->Localizations[$languagePreference->Language]))
                    {
                        $selected_localization = $this->ResourcesPath . DIRECTORY_SEPARATOR . $this->LocalizationConfiguration->Localizations[$languagePreference->Language];

                        // Load the selected localization if it exists.
                        if(file_exists($selected_localization))
                        {
                            $this->SelectedLanguage = \DynamicalWeb\Objects\Localization::fromFile($selected_localization);
                        }

                        break;
                    }
                }

            }

            $this->Enabled = true;
        }


        /**
         * Gets an existing localization from the web application
         *
         * @param string $language
         * @return \DynamicalWeb\Objects\Localization
         * @throws LocalizationException
         * @noinspection PhpUnused
         */
        public function getLocalization(string $language): \DynamicalWeb\Objects\Localization
        {
            if(isset($this->LocalizationConfiguration->Localizations[$language]) == false)
                throw new LocalizationException('The requested localization \'' . $language . '\' is not defined');

            $path = $this->ResourcesPath . DIRECTORY_SEPARATOR .  $this->LocalizationConfiguration->Localizations[$language];

            if(file_exists($path) == false)
                throw new LocalizationException('The localization file \'' . $path . '\' was not found');
            return \DynamicalWeb\Objects\Localization::fromFile($path);
        }

        /**
         * Detects the client's preferred client languages
         *
         * @return LanguagePreference[]
         * @noinspection PhpUnusedLocalVariableInspection
         */
        public static function detectPreferredClientLanguages(): array
        {
            preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]), $matches, PREG_SET_ORDER);
            $return_values = [];
            $detected = [];
            foreach($matches as $match)
            {

                list($a, $b) = explode('-', $match[1]) + array('', '');
                $value = isset($match[2]) ? (float) $match[2] : 1.0;

                if(in_array($match[1], $detected) == false)
                {
                    $return_values[] = LanguagePreference::parse($match[1] . '=' . $value);
                    $detected[] = $match[1];
                }

                if(in_array($a, $detected) == false)
                {
                    $return_values[] = LanguagePreference::parse($a . '=' . ($value - 0.1));
                    $detected[] = $a;
                }
            }

            arsort($return_values);
            return $return_values;
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
            define('DYNAMICAL_LOCALIZATION_COOKIE', 'language_' . $this->WebApplicationNameSafe);
            define('DYNAMICAL_PRIMARY_LOCALIZATION', $this->PrimaryLanguage->Language);
            define('DYNAMICAL_PRIMARY_LOCALIZATION_PATH', $this->PrimaryLanguage->SourcePath);
            define('DYNAMICAL_PRIMARY_LOCALIZATION_ISO_CODE', $this->PrimaryLanguage->IsoCode);
            define('DYNAMICAL_SELECTED_LOCALIZATION', $this->SelectedLanguage->Language);
            define('DYNAMICAL_SELECTED_LOCALIZATION_PATH', $this->SelectedLanguage->SourcePath);
            define('DYNAMICAL_SELECTED_LOCALIZATION_ISO_CODE', $this->SelectedLanguage->IsoCode);

            $preferred_language = null;
            $preferred_alt_language = null;
            $secondary_preference = null;
            $secondary_alt_preference = null;

            foreach(self::detectPreferredClientLanguages() as $languagePreference)
            {
                if($preferred_language == null)
                {
                    $preferred_language = $languagePreference->Language;
                    $preferred_alt_language = $languagePreference->LanguageCode;
                    continue;
                }

                if($secondary_preference == null && $languagePreference->Language !== $preferred_language)
                {
                    $secondary_preference = $languagePreference->Language;
                    $secondary_alt_preference = $languagePreference->LanguageCode;
                    break;
                }
            }

            if($secondary_preference == null)
            {
                $secondary_preference = $preferred_language;
                $secondary_alt_preference = $preferred_alt_language;
            }

            define('DYNAMICAL_CLIENT_PREFERRED_PRIMARY_LOCALIZATION', $preferred_language);
            define('DYNAMICAL_CLIENT_PREFERRED_PRIMARY_ALT_LOCALIZATION', $preferred_alt_language);
            define('DYNAMICAL_CLIENT_PREFERRED_SECONDARY_LOCALIZATION', $secondary_preference);
            define('DYNAMICAL_CLIENT_PREFERRED_SECONDARY_ALT_LOCALIZATION', $secondary_alt_preference);

            // Set the global variables
            DynamicalWeb::setMemoryObject('app_localization_primary', $this->PrimaryLanguage);
            DynamicalWeb::setMemoryObject('app_localization_selected', $this->SelectedLanguage);
            DynamicalWeb::setMemoryObject('app_localization_configuration', $this->LocalizationConfiguration);

            $router->map('GET|POST', 'dyn/lang', function()
            {
                $client_request = DynamicalWeb::constructRequestHandler();

                $client_request->ResourceSource = ResourceSource::Memory;
                $client_request->Source = 'Loading...';
                $client_request->ResponseContentType = BuiltinMimes::Html;
                DynamicalWeb::activeRequestHandler($client_request);

                if(Request::getParameter('value') !== null)
                {
                    Localization::changeLanguage(Request::getParameter('value'), false);
                }

                return DynamicalWeb::activeRequestHandler();
            }, 'change_language');
        }

        /**
         * Sets the language cookie
         *
         * @throws WebApplicationException
         */
        public static function setCookie()
        {
            if(DYNAMICAL_LOCALIZATION_ENABLED)
            {
                /** @var \DynamicalWeb\Objects\Localization $Localization */
                $Localization = DynamicalWeb::getMemoryObject('app_localization_selected');
                Cookies::setCookie(new Cookie('language_' . DYNAMICAL_APP_NAME_SAFE, $Localization->IsoCode));
            }
        }

        /**
         * Changes the current language set to the web application
         *
         * @param string $language
         * @param bool $throw_errors
         * @throws LocalizationException
         * @throws RouterException
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
            /** @var WebApplication\LocalizationConfiguration $localization_configuration */
            $localization_configuration = DynamicalWeb::getMemoryObject('app_localization_configuration');

            if(isset($localization_configuration->Localizations[$selected_language]) == false)
            {
                if($throw_errors)
                    throw new LocalizationException('The requested localization \'' . $selected_language . '\' is not defined');
                return;
            }

            $path = DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR .  $localization_configuration->Localizations[$selected_language];

            if(file_exists($path) == false)
            {
                if($throw_errors)
                    throw new LocalizationException('The localization file \'' . $path . '\' was not found');
                return;
            }

            DynamicalWeb::setMemoryObject('app_localization_selected', \DynamicalWeb\Objects\Localization::fromFile($path));

            self::setCookie();
            Actions::redirect(DynamicalWeb::getRoute(DYNAMICAL_HOME_PAGE));
        }

        /**
         * Gets the route for changing the current localization
         *
         * @param string $language
         * @return string
         * @throws RouterException
         */
        public static function getRoute(string $language): string
        {
            /** @var Router $router */
            $router = DynamicalWeb::getMemoryObject('app_router');
            $url = $router->generate('change_language');
            $url .= '?' . http_build_query([
                't'=>hash('crc32', time()),
                'value' => $language
            ]);
            return $url;
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

        /**
         * @return WebApplication\LocalizationConfiguration
         * @noinspection PhpUnused
         */
        public function getLocalizationConfiguration(): WebApplication\LocalizationConfiguration
        {
            return $this->LocalizationConfiguration;
        }
    }