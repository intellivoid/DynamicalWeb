<?php

    namespace DynamicalWeb;

    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\Classes\Localization;
    use DynamicalWeb\Exceptions\WebApplicationException;

    class Html
    {
        /**
         * Prints content out to be HTML friendly
         *
         * @param string $input
         * @param bool $escape
         * @param string $encoding
         */
        public static function print(string $input, bool $escape=true, string $encoding='UTF-8')
        {
            if($escape)
                $input = htmlspecialchars($input, ENT_QUOTES, $encoding);
            print($input);
        }

        /**
         * Imports a section and localization data
         *
         * @param string $section_name
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         */
        public static function importSection(string $section_name)
        {
            // Search in the page first
            $path = 'sections' . DIRECTORY_SEPARATOR . $section_name . '.dyn';

            if(defined('DYNAMICAL_CURRENT_PAGE_PATH') && file_exists(DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path))
            {
                Localization::loadLocalization(LocalizationSection::Section, $section_name, false);
                include(DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path);
                return;
            }

            if(defined('DYNAMICAL_APP_RESOURCES_PATH') && file_exists(DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path))
            {
                Localization::loadLocalization(LocalizationSection::Section, $section_name, false);
                include(DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path);
                return;
            }

            throw new WebApplicationException('Cannot import section \'' . $section_name . '\', the file was not found');
        }
    }