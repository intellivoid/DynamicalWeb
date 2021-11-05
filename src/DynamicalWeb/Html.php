<?php

    namespace DynamicalWeb;

    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\Classes\Localization;
    use DynamicalWeb\Classes\Utilities;
    use DynamicalWeb\Classes\WebAssets;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use MarkdownParser\MarkdownParser;

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

            $path = 'sections' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($section_name) . '.dyn';

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

            if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinSections' . DIRECTORY_SEPARATOR . $path))
            {
                Localization::loadLocalization(LocalizationSection::Section, $section_name, false);
                include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinSections' . DIRECTORY_SEPARATOR . $path);
                return;
            }

            throw new WebApplicationException('Cannot import section \'' . $section_name . '\', the file was not found');
        }

        /**
         * Imports a executable markdown file
         *
         * @param string $document_name
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         */
        public static function importMarkdown(string $document_name)
        {
            // Search in the page first
            $path = 'markdown' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($document_name) . '.md.dyn';

            $selected_path = null;
            if(defined('DYNAMICAL_CURRENT_PAGE_PATH') && file_exists(DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path))
            {
                $selected_path = DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path;
            }

            if($selected_path == null)
            {
                if(defined('DYNAMICAL_APP_RESOURCES_PATH') && file_exists(DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path))
                {
                    $selected_path = DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path;
                }
            }

            if($selected_path == null)
            {
                if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinMarkdown' . DIRECTORY_SEPARATOR . $path))
                {
                    Localization::loadLocalization(LocalizationSection::Section, $document_name, false);
                    include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinMarkdown' . DIRECTORY_SEPARATOR . $path);
                    return;
                }
            }

            if($selected_path == null)
                throw new WebApplicationException('Cannot import markdown \'' . $document_name . '\', the file was not found');

            Localization::loadLocalization(LocalizationSection::Markdown, $document_name, false);

            ob_start();
            include($selected_path);
            $markdown_content = ob_get_clean();
            $markdown_parser = new MarkdownParser();
            print($markdown_parser->parse($markdown_content));
        }


    }