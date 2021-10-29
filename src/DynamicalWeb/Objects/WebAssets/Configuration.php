<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebAssets;

    class Configuration
    {
        /**
         * The path of the assets directory
         *
         * @var string
         */
        public $Path;

        /**
         * The route for the assets
         *
         * @var string
         */
        public $Route;

        /**
         * Indicates if CSS content should be compressed or not
         *
         * @var bool
         */
        public $CompressCss;

        /**
         * Indicates if JS content should be compressed or not
         *
         * @var bool
         */
        public $CompressJs;

        /**
         * Returns an array representation
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'path' => $this->Path,
                'route' => $this->Route,
                'compress_css' => $this->CompressCss,
                'compress_js' => $this->CompressJs
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return Configuration
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): Configuration
        {
            $configuration = new Configuration();

            if(isset($data['path']))
                $configuration->Path = $data['path'];

            if(isset($data['route']))
                $configuration->Route = $data['route'];

            if(isset($data['compress_css']))
                $configuration->CompressCss = $data['compress_css'];

            if(isset($data['compress_js']))
                $configuration->CompressJs = $data['compress_js'];

            return $configuration;
        }
    }