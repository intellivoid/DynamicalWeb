<?php

    namespace DynamicalWeb\Classes\UserAgentParser;

    use DynamicalWeb\Abstracts\AbstractParser;
    use DynamicalWeb\Objects\UserAgentOperatingSystem;

    class OperatingSystemParser extends AbstractParser
    {

        /**
         * Attempts to see if the user agent matches an operating system regex
         *
         * @param string $userAgent
         * @return UserAgentOperatingSystem
         */
        public function parseOperatingSystem(string $userAgent): UserAgentOperatingSystem
        {
            $os = new UserAgentOperatingSystem();

            [$regex, $matches] = self::tryMatch($this->regexes['os_parsers'], $userAgent);

            if ($matches) {
                $os->family = self::multiReplace($regex, 'os_replacement', $matches[1], $matches) ?? $os->family;
                $os->major = self::multiReplace($regex, 'os_v1_replacement', $matches[2], $matches);
                $os->minor = self::multiReplace($regex, 'os_v2_replacement', $matches[3], $matches);
                $os->patch = self::multiReplace($regex, 'os_v3_replacement', $matches[4], $matches);
                $os->patchMinor = self::multiReplace($regex, 'os_v4_replacement', $matches[5], $matches);
            }

            return $os;
        }
    }
