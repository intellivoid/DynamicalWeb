<?php

    namespace DynamicalWeb;

    /**
     * Basic HTML Utilities for rendering
     *
     * Class HTML
     * @package DynamicalWeb
     */
    class HTML
    {
        /**
         * Prints HTML output
         *
         * @param string $output
         * @param bool $escape_html
         */
        public static function print(string $output, bool $escape_html = true)
        {
            if($escape_html == true)
            {
                $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
            }

            print($output);
        }

        /**
         * Imports a file from the sections directory in resources
         *
         * @param string $sectionName
         * @throws \Exception
         */
        public static function importSection(string $sectionName)
        {
            $FormattedName = strtolower(stripslashes($sectionName));

            if(file_exists(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . $FormattedName . '.php') == false)
            {
                throw new \Exception('The section file "' .  $FormattedName . '.php" was not found');
            }

            Language::loadSection($sectionName);

            /** @noinspection PhpIncludeInspection */
            include_once(APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . $FormattedName . '.php');
        }


        public static function importHTML(string $resourceName)
        {
            $FormattedName = strtolower(stripslashes($resourceName));

            $LocalResource = APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $FormattedName . '.php';
            $SharedResource = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'shared' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $FormattedName . '.php';

            if(file_exists($LocalResource) == false)
            {
                if(file_exists($SharedResource) == false)
                {
                    throw new \Exception('The resource file "' . $FormattedName . '.php" was not found in either local resources or shared resources');
                }
                else
                {
                    /** @noinspection PhpIncludeInspection */
                    include_once($SharedResource);
                    return ;
                }
            }
            else
            {
                /** @noinspection PhpIncludeInspection */
                include_once($LocalResource);
            }
        }

        /**
         * Imports a script from local resources or shared resources
         *
         * @param string $sectionName
         * @throws \Exception
         */
        public static function importScript(string $sectionName)
        {
            $FormattedName = strtolower(stripslashes($sectionName));

            $LocalResource = APP_CURRENT_PAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $FormattedName . '.php';
            $SharedResource = APP_RESOURCES_DIRECTORY . DIRECTORY_SEPARATOR . 'shared' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $FormattedName . '.php';

            if(file_exists($LocalResource) == false)
            {
                if(file_exists($SharedResource) == false)
                {
                    throw new \Exception('The resource file "' . $FormattedName . '.php" was not found in either local resources or shared resources');
                }
                else
                {
                    /** @noinspection PhpIncludeInspection */
                    include_once($SharedResource);
                    return ;
                }
            }
            else
            {
                /** @noinspection PhpIncludeInspection */
                include_once($LocalResource);
            }
        }
    }