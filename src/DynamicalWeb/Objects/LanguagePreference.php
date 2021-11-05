<?php

    /** @noinspection PhpUnused */
    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects;

    class LanguagePreference
    {
        /**
         * The full language code
         *
         * @var string
         */
        public $LanguageCode;

        /**
         * The language itself
         *
         * @var string
         */
        public $Language;

        /**
         * The directive language
         *
         * @var string|null
         */
        public $Directive;

        /**
         * @var float|int
         */
        public $Priority;

        public static function parse(string $input): LanguagePreference
        {
            $LanguagePreference = new LanguagePreference();
            $split_value = explode('=', $input);

            // If the browser's developer is a dumb shit and sends just one value as 'en'
            if(count($split_value) == 1 || count($split_value) == 0)
            {
                $LanguagePreference->LanguageCode = $input;
                $LanguagePreference->Priority = 1;
                $LanguagePreference->Language = $input;
                $LanguagePreference->Directive = null;

                return $LanguagePreference;
            }

            $LanguagePreference->LanguageCode = $split_value[0];
            $LanguagePreference->Priority = (float)$split_value[1];

            // Sometimes this value is not always there
            $split_directive = explode('-', $split_value[0]);
            if(count($split_directive) == 1 || count($split_directive) == 0)
            {
                $LanguagePreference->Language = $LanguagePreference->LanguageCode;
                $LanguagePreference->Directive = null;
                return $LanguagePreference;
            }

            $LanguagePreference->Language = $split_directive[0];
            $LanguagePreference->Directive = $split_directive[1];

            return $LanguagePreference;
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return [
                'language_code' => $this->LanguageCode,
                'language' => $this->Language,
                'directive' => $this->Directive,
                'priority' => $this->Priority
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return LanguagePreference
         */
        public static function fromArray(array $data): LanguagePreference
        {
            $LanguagePreference = new LanguagePreference();

            if(isset($data['language_code']))
                $LanguagePreference->LanguageCode = $data['language_code'];

            if(isset($data['language']))
                $LanguagePreference->Language = $data['language'];

            if(isset($data['directive']))
                $LanguagePreference->Directive = $data['directive'];

            if(isset($data['priority']))
                $LanguagePreference->Priority = $data['priority'];

            return $LanguagePreference;
        }
    }