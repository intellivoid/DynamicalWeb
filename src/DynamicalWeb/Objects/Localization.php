<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Exceptions\LocalizationException;
    use DynamicalWeb\Objects\Localization\LocalizationText;

    class Localization
    {
        /**
         * The name of the language
         *
         * @var string
         */
        public $Language;

        /**
         * The ISO_639-1 Code of the language
         *
         * @var string
         */
        public $IsoCode;

        /**
         * The page localizations
         *
         * @var LocalizationText[]|array
         */
        public $PageLocalizations;

        /**
         * The section localizations
         *
         * @var LocalizationText[]|array
         */
        public $SectionLocalizations;

        /**
         * The source of the localization object
         *
         * @var string|null
         */
        public $SourcePath;

        /**
         * Returns the localization text for a page, returns null if not set.
         *
         * @param string $page
         * @param string $name
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getPageLocalization(string $page, string $name): ?string
        {
            if(isset($this->PageLocalizations[$page]) == false)
                return null;

            if(isset($this->PageLocalizations[$page][$name]) == false)
                return null;

            /** @var LocalizationText $name */
            return $this->PageLocalizations[$page][$name]->Text;
        }

        /**
         * Returns the localization text for a page, returns null if not set.
         *
         * @param string $section
         * @param string $name
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getSectionLocalization(string $section, string $name): ?string
        {
            if(isset($this->SectionLocalizations[$section]) == false)
                return null;

            if(isset($this->SectionLocalizations[$section][$name]) == false)
                return null;

            /** @var LocalizationText $name */
            return $this->SectionLocalizations[$section][$name]->Text;
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            $page_localizations = [];
            foreach($this->PageLocalizations as $page => $localization)
            {
                $page_localizations[$page] = [];
                /** @var LocalizationText $datum */
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach($localization as $name => $datum)
                {
                    $page_localizations[$page][] = $datum->toArray();
                }
            }

            $section_localizations = [];
            foreach($this->SectionLocalizations as $section => $localization)
            {
                $section_localizations[$section] = [];
                /** @var LocalizationText $datum */
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach($localization as $name => $datum)
                {
                    $section_localizations[$section][] = $datum->toArray();
                }
            }

            return [
                'name' => $this->Language,
                'iso_639-1' => $this->IsoCode,
                'pages' => $page_localizations,
                'sections' => $section_localizations,
                'source_path' => $this->SourcePath
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return Localization
         */
        public static function fromArray(array $data): Localization
        {
            $LocalizationObject = new Localization();

            if(isset($data['name']))
                $LocalizationObject->Language = $data['name'];

            if(isset($data['iso_639-1']))
                $LocalizationObject->IsoCode = $data['iso_639-1'];

            $LocalizationObject->PageLocalizations = [];
            $LocalizationObject->SectionLocalizations = [];

            if(isset($data['pages']))
            {
                foreach($data['pages'] as $page => $datum)
                {
                    $LocalizationObject->PageLocalizations[$page] = [];
                    foreach($datum as $name => $value)
                    {
                        $localized_text = LocalizationText::fromInput($name, $value);
                        $LocalizationObject->PageLocalizations[$page][$localized_text->Name] = $localized_text;
                    }
                }
            }

            if(isset($data['sections']))
            {
                foreach($data['sections'] as $section => $datum)
                {
                    $LocalizationObject->SectionLocalizations[$section] = [];
                    foreach($datum as $name => $value)
                    {
                        $localized_text = LocalizationText::fromInput($name, $value);
                        $LocalizationObject->SectionLocalizations[$section][$localized_text->Name] = $localized_text;
                    }
                }
            }

            if(isset($data['source_path']))
                $LocalizationObject->SourcePath = $data['source_path'];

            return $LocalizationObject;
        }

        /**
         * Constructs object from a file path
         *
         * @param string $path
         * @return Localization
         * @throws LocalizationException
         */
        public static function fromFile(string $path): Localization
        {
            $file_contents = file_get_contents($path);
            $file_decoded = json_decode($file_contents, true);

            if($file_decoded == false)
                throw new LocalizationException('The localization file \'' . $path . '\' contains invalid JSON data');

            $return_object = Localization::fromArray($file_decoded);
            $return_object->SourcePath = $path;

            return $return_object;
        }
    }