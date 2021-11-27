<?php

    namespace DynamicalWeb;

    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\Classes\Localization;
    use DynamicalWeb\Classes\Utilities;
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

            if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinSections' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($section_name) . '.dyn'))
            {
                Localization::loadLocalization(LocalizationSection::Section, $section_name, false);
                include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinSections' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($section_name) . '.dyn');
                return;
            }

            throw new WebApplicationException('Cannot import section \'' . $section_name . '\', the file was not found');
        }

        /**
         * Imports an executable markdown file
         *
         * @param string $document_name
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         * @noinspection DuplicatedCode
         * @noinspection PhpIncludeInspection
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
                if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinMarkdown' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($document_name) . '.md.dyn'))
                {
                    Localization::loadLocalization(LocalizationSection::Section, $document_name, false);
                    include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinMarkdown' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($document_name) . '.md.dyn');
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

        /**
         * Imports a pure PHP script
         *
         * @param string $script_name
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         * @noinspection PhpUnused
         * @noinspection PhpIncludeInspection
         */
        public static function importScript(string $script_name)
        {
            // Search in the page first
            $path = 'scripts' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.dyn';

            if(defined('DYNAMICAL_CURRENT_PAGE_PATH') && file_exists(DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path))
            {
                Localization::loadLocalization(LocalizationSection::Custom, 'script_' . $script_name, false);
                include(DYNAMICAL_CURRENT_PAGE_PATH . DIRECTORY_SEPARATOR . $path);
                return;
            }

            if(defined('DYNAMICAL_APP_RESOURCES_PATH') && file_exists(DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path))
            {
                Localization::loadLocalization(LocalizationSection::Custom, 'script_' . $script_name, false);
                include(DYNAMICAL_APP_RESOURCES_PATH . DIRECTORY_SEPARATOR . $path);
                return;
            }

            if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinScripts' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.dyn'))
            {
                Localization::loadLocalization(LocalizationSection::Custom, 'script_' . $script_name, false);
                include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinScripts' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.dyn');
                return;
            }

            throw new WebApplicationException('Cannot import script \'' . $script_name . '\', the file was not found');
        }

        /**
         * Imports and executes compiled javascript code
         *
         * @param string $script_name
         * @param bool $include_tags
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         * @noinspection PhpUnused
         * @noinspection DuplicatedCode
         * @noinspection PhpIncludeInspection
         */
        public static function importJavascript(string $script_name, bool $include_tags=true)
        {
            // Search in the page first
            $path = 'javascript' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.js.dyn';

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
                if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinJavascript' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.js.dyn'))
                {
                    Localization::loadLocalization(LocalizationSection::Section, $script_name, false);
                    include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinJavascript' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.js.dyn');
                    return;
                }
            }

            if($selected_path == null)
                throw new WebApplicationException('Cannot import javascript \'' . $script_name . '\', the file was not found');

            Localization::loadLocalization(LocalizationSection::Custom, 'javascript_' . $script_name, false);

            ob_start();
            include($selected_path);
            if($include_tags)
            {
                print('<script>' . ob_get_clean() . '</script>');
            }
            else
            {
                print(ob_get_clean());
            }
        }


        /**
         * Imports and executes compiled javascript code
         *
         * @param string $script_name
         * @param bool $include_tags
         * @throws Exceptions\LocalizationException
         * @throws WebApplicationException
         * @noinspection PhpUnused
         * @noinspection DuplicatedCode
         * @noinspection PhpIncludeInspection
         */
        public static function importCss(string $script_name, bool $include_tags=true)
        {
            // Search in the page first
            $path = 'css' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.css.dyn';

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
                if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinCss' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.css.dyn'))
                {
                    Localization::loadLocalization(LocalizationSection::Section, $script_name, false);
                    include(__DIR__ . DIRECTORY_SEPARATOR . 'BuiltinCss' . DIRECTORY_SEPARATOR . Utilities::getAbsolutePath($script_name) . '.css.dyn');
                    return;
                }
            }

            if($selected_path == null)
                throw new WebApplicationException('Cannot import CSS \'' . $script_name . '\', the file was not found');

            Localization::loadLocalization(LocalizationSection::Custom, 'css_' . $script_name, false);

            ob_start();
            include($selected_path);
            if($include_tags)
            {
                print('<style>' . ob_get_clean() . '</style>');
            }
            else
            {
                print(ob_get_clean());
            }
        }


    }