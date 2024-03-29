<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\BuiltinMimes;
    use DynamicalWeb\Abstracts\LocalizationSection;
    use DynamicalWeb\Abstracts\ResourceSource;
    use DynamicalWeb\Abstracts\RuntimeEvent;
    use DynamicalWeb\DynamicalWeb;
    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Exceptions\PageNotFoundException;
    use DynamicalWeb\Exceptions\RouterException;
    use DynamicalWeb\Exceptions\WebApplicationConfigurationException;
    use DynamicalWeb\Exceptions\WebApplicationException;
    use DynamicalWeb\Objects\PathIndex;
    use DynamicalWeb\Objects\WebApplication\Route;
    use DynamicalWeb\Objects\WebApplication\RuntimeScript;

    class PageIndexes
    {
        /**
         * @var string
         */
        private $PagesPath;

        /**
         * A path index representation of the
         *
         * @var PathIndex[]
         */
        private $Index;

        /**
         * @var string
         */
        private string $WebApplicationName;

        /**
         * @param string $web_application_name
         * @param string $resources_path
         * @param Route[] $routes
         * @throws WebApplicationConfigurationException
         * @throws WebApplicationException
         */
        public function __construct(string $web_application_name, string $resources_path, array $routes)
        {
            $this->PagesPath = $resources_path . DIRECTORY_SEPARATOR . 'pages';
            $this->WebApplicationName = $web_application_name;
            $this->Index = [];
            $ProcessedPages = [];

            if(is_dir($this->PagesPath) == false)
                throw new WebApplicationException('The pages directory does not exist in ' . $this->PagesPath);

            // Acceptable files for main content pages
            $main_files = [
                'index.php',
                'contents.php',
                'main.php',
                'index.dyn',
                'contents.dyn',
                'main.dyn',
                'index.php.dyn',
                'contents.php.dyn',
                'main.php.dyn'
            ];

            $builtin_pages = [
                '404',
                '500',
                'dynamical'
            ];

            foreach($routes as $route)
            {
                if(in_array($route->Page, $ProcessedPages))
                    throw new WebApplicationConfigurationException('Duplicate route for \'' . $route->Page . '\' (\'' . $route->Path . '\')');

                $route_path = $this->PagesPath . DIRECTORY_SEPARATOR . stripslashes($route->Page);

                $execution_point = null;
                if(is_dir($route_path) == false)
                    throw new WebApplicationConfigurationException('The route for \'' . $route->Page . '\' (\'' . $route->Path . '\'), does not exist in \'' . $route_path . '\'');

                foreach($main_files as $file)
                {
                    if(file_exists($route_path . DIRECTORY_SEPARATOR . $file))
                    {
                        $execution_point = realpath($route_path . DIRECTORY_SEPARATOR . $file);
                        break;
                    }
                }

                if($execution_point == null)
                    throw new WebApplicationConfigurationException('The route for \'' . $route->Page . '\' (\'' . $route->Path . '\'), contains no execution point \'' . $route_path . '\'');


                $PathIndex = new PathIndex();
                $PathIndex->Route = $route;
                $PathIndex->PageExecutionPoint = $execution_point;
                $PathIndex->PagePath = $route_path;

                $this->Index[] = $PathIndex;
                $ProcessedPages[] = $route->Page;
            }

            // Add builtin pages
            foreach($builtin_pages as $page)
            {
                if(in_array($page, $ProcessedPages))
                    continue; // Skip, don't overwrite app pages with builtin pages.
                $PathIndex = new PathIndex();
                $PathIndex->Route = new Route();
                $PathIndex->Route->RequestMethods = ['GET'];
                $PathIndex->Route->Path = $page;
                $PathIndex->Route->Page = $page;
                $PathIndex->Route->InlineParameters = [];
                $PathIndex->PagePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'BuiltinPages' . DIRECTORY_SEPARATOR . $page);
                $PathIndex->PageExecutionPoint = realpath($PathIndex->PagePath . DIRECTORY_SEPARATOR . 'contents.dyn');
                $this->Index[] = $PathIndex;
                $ProcessedPages[] = $page;
            }
        }

        /**
         * Initializes the page index for the web application
         *
         * @param Router $router
         * @throws RouterException
         * @throws WebApplicationException
         * @author Kasper Medvedkov <@AntiEngineer>
         * @noinspection DuplicatedCode
         */
        public function initialize(Router &$router)
        {
            if(defined('DYNAMICAL_INITIALIZED'))
                throw new WebApplicationException('Cannot initialize ' . $this->WebApplicationName . ', another web application is already initialized');

            DynamicalWeb::setMemoryObject('app_pages_index', $this->Index);

            define('DYNAMICAL_PAGES_PATH', $this->PagesPath);
            define('DYNAMICAL_HOME_PAGE', $this->Index[0]->Route->Page);

            // Map the routes
            foreach($this->Index as $index)
            {
                $Route = $index->Route;
                $URI = $Route->Path;
                preg_match_all("/%.+?/", $URI, $Para, PREG_PATTERN_ORDER);
                $FinalURI = $URI;

                if(count($Route->InlineParameters) > 0)
                {
                    // DX000000182 kasper.medvedkov     Count matches from CFG to the actual URI/Params, if not matched, return 500 with information about parameter misconfig //
                    $match = count($Para[0]) == count($Route->InlineParameters);

                    if (!$match)
                        throw new RouterException(
                            'The amount of parameters that should be received doesn\'t match with the amount of parameters configured on ' . $Route->Page . ', check the configuration for errors.');

                    $Dx = array_combine($Para[0], $Route->InlineParameters);
                    foreach($Dx as $ParamTag => $ParamKey)
                    {
                        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
                        switch($ParamTag)
                        {
                            case"%s":
                                $FinalURI = str_replace("%s", "[*:$ParamKey]", $FinalURI);
                                break;

                            case "%i":
                                $FinalURI = str_replace("%i", "[i:$ParamKey]", $FinalURI);
                                break;

                            case "%h":
                                $FinalURI = str_replace("%h", "[h:$ParamKey]", $FinalURI);
                                break;

                            case "%a":
                                $FinalURI = str_replace("%h", "[**:$ParamKey]", $FinalURI);
                                break;
                        }
                    }
                }

                if(in_array('HEAD', $Route->RequestMethods) == false)
                    $Route->RequestMethods[] = 'HEAD';
                $router->map(implode("|", $Route->RequestMethods), $FinalURI, function() use ($Route)
                {
                    $client_request = DynamicalWeb::constructRequestHandler();

                    $client_request->ResourceSource = ResourceSource::Page;
                    $client_request->Source = $Route->Page;
                    $client_request->ResponseCode = 200;
                    $client_request->ResponseContentType = BuiltinMimes::Html;

                    return $client_request;
                }, $Route->Page);
            }
        }

        /**
         * Returns a path index page
         *
         * @param string $page
         * @return PathIndex|null
         * @throws WebApplicationException
         */
        public static function get(string $page): ?PathIndex
        {
            if(defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('The function PageIndexes::get() cannot be invoked without a initialized web application');

            /** @var PathIndex $page_index */
            foreach(DynamicalWeb::getMemoryObject('app_pages_index') as $page_index)
            {
                if($page == $page_index->Route->Page)
                    return $page_index;
            }

            return null;
        }

        /**
         * Loads and executes a page
         *
         * @param string $page
         * @throws PageNotFoundException
         * @throws WebApplicationException
         * @throws LocalizationException
         */
        public static function load(string $page)
        {
            if (defined('DYNAMICAL_INITIALIZED') == false)
                throw new WebApplicationException('The function PageIndexes::load() cannot be invoked without a initialized web application');

            $define_page = true;
            if (defined('DYNAMICAL_CURRENT_PAGE')) {
                if ($page == '500' || $page == '404') {
                    $define_page = false;
                } else {
                    throw new WebApplicationException('The function PageIndexes::load() cannot be invoked twice');
                }
            }

            $page_index = self::get($page);
            if ($page_index == null)
                throw new PageNotFoundException('The requested page \'' . $page . '\' was not found');

            // Set the definitions for the page
            if ($define_page) {
                define('DYNAMICAL_CURRENT_PAGE', $page);
                define('DYNAMICAL_CURRENT_PAGE_PATH', $page_index->PagePath);
                define('DYNAMICAL_CURRENT_PAGE_EXECUTION_POINT', $page_index->PageExecutionPoint);
                define('DYNAMICAL_CURRENT_PAGE_ROUTE_PATH', $page_index->Route->Path);
            }

            // Load the localization for the page
            Localization::loadLocalization(LocalizationSection::Page, $page, false);

            if (DynamicalWeb::getMemoryObject('app_runtime_scripts') !== null)
            {
                /** @var RuntimeScript $runtime_script */
                foreach(DynamicalWeb::getMemoryObject('app_runtime_scripts') as $runtime_script)
                {
                    if($runtime_script->Event == RuntimeEvent::PrePageLoad && $runtime_script->hasExecuted() == false)
                    {
                        $runtime_script->execute();
                    }
                }
            }

            include($page_index->PageExecutionPoint);

            if(DynamicalWeb::getMemoryObject('app_runtime_scripts') !== null)
            {
                /** @var RuntimeScript $runtime_script */
                foreach(DynamicalWeb::getMemoryObject('app_runtime_scripts') as $runtime_script)
                {
                    if($runtime_script->Event == RuntimeEvent::PostPageLoad && $runtime_script->hasExecuted() == false)
                    {
                        $runtime_script->execute();
                    }
                }
            }

        }

    }