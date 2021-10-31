<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    use DynamicalWeb\Abstracts\WebAssetType;

    class WebAssetConfiguration
    {
        /**
         * The type of web asset, if it's local or from ppm
         *
         * @var string|WebAssetType
         */
        public $Type;

        /**
         * The source of the web asset, the ppm package name or directory
         *
         * @var string
         */
        public $Source;

        /**
         * The routed path that the assets can be accessed from via HTTP
         *
         * @var string
         */
        public $Path;

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'type' => $this->Type,
                'source' => $this->Source,
                'path' => $this->Path
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return WebAssetConfiguration
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): WebAssetConfiguration
        {
            $WebAssetsConfiguration = new WebAssetConfiguration();

            if(isset($data['type']))
                $WebAssetsConfiguration->Type = $data['type'];

            if(isset($data['source']))
                $WebAssetsConfiguration->Source = $data['source'];

            if(isset($data['path']))
                $WebAssetsConfiguration->Path = $data['path'];

            return $WebAssetsConfiguration;
        }
    }