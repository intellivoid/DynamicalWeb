<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\Localization;

    class LocalizationText
    {
        /**
         * The name of the localized text
         *
         * @var string
         */
        public $Name;

        /**
         * The text value of the localized text
         *
         * @var string
         */
        public $Text;

        /**
         * Returns an array representation of the localization text
         *
         * @return string[]
         */
        public function toArray(bool $assoc=false, array &$array = []): array
        {
            if($assoc)
            {
                $array[$this->Name] = $this->Text;
                return $array;
            }

            return [$this->Name => $this->Text];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return LocalizationText
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): LocalizationText
        {
            $LocalizationTextObject = new LocalizationText();

            $LocalizationTextObject->Name = array_key_first($data);
            $LocalizationTextObject->Text = $data[$LocalizationTextObject->Name];

            return $LocalizationTextObject;
        }

        /**
         * Constructs the object from two inputs
         *
         * @param string $name
         * @param string $value
         * @return LocalizationText
         */
        public static function fromInput(string $name, string $value): LocalizationText
        {

            $LocalizationTextObject = new LocalizationText();

            $LocalizationTextObject->Name = $name;
            $LocalizationTextObject->Text = $value;

            return $LocalizationTextObject;
        }
    }