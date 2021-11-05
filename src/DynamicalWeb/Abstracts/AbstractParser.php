<?php

    namespace DynamicalWeb\Abstracts;


    abstract class AbstractParser
    {
        /**
         * @var array
         */
        protected $regexes = [];

        public function __construct(array $regexes)
        {
            $this->regexes = $regexes;
        }

        /**
         * @param array $regexes
         * @param string $userAgent
         * @return array
         */
        protected static function tryMatch(array $regexes, string $userAgent): array
        {
            foreach ($regexes as $regex) {
                if (preg_match($regex['regex'], $userAgent, $matches)) {

                    $defaults = [
                        1 => 'Other',
                        2 => null,
                        3 => null,
                        4 => null,
                        5 => null,
                    ];

                    return [$regex, $matches + $defaults];
                }
            }

            return [null, null];
        }

        /**
         * @param array $regex
         * @param string $key
         * @param string|null $default
         * @param array $matches
         * @return string|null
         */
        protected static function multiReplace(array $regex, string $key, ?string $default, array $matches): ?string
        {
            if (!isset($regex[$key])) {
                return self::emptyStringToNull($default);
            }

            $replacement = preg_replace_callback(
                '|\$(?P<key>\d)|',
                static function ($m) use ($matches) {
                    return $matches[$m['key']] ?? '';
                },
                $regex[$key]
            );

            return self::emptyStringToNull($replacement);
        }

        /**
         * @param string|null $string
         * @return string|null
         */
        private static function emptyStringToNull(?string $string): ?string
        {
            $string = trim($string ?? '');

            return $string === '' ? null : $string;
        }
    }
