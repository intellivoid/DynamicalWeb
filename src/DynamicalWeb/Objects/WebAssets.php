<?php

    /** @noinspection PhpMissingFieldTypeInspection */
    /** @noinspection PhpUnused */

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Objects\WebAssets\Configuration;

    class WebAssets
    {
        /**
         * @var string
         */
        public $Name;

        /**
         * @var Configuration
         */
        public $Configuration;

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'name' => $this->Name,
                'configuration' => $this->Configuration->toArray()
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return WebAssets
         */
        public static function fromArray(array $data): WebAssets
        {
            $WebAssets = new WebAssets();

            if(isset($data['name']))
                $WebAssets->Name = $data['name'];

            if(isset($data['configuration']))
                $WebAssets->Configuration = Configuration::fromArray($data['configuration']);

            return $WebAssets;
        }
    }