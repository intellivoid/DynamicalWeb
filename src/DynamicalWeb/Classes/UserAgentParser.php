<?php

    namespace DynamicalWeb\Classes;

    use DynamicalWeb\Abstracts\AbstractParser;
    use DynamicalWeb\Objects\UserAgent;

    class UserAgentParser extends AbstractParser
    {
        /**
         * Attempts to see if the user agent matches a user agents regex
         * @noinspection PhpStrFunctionsInspection
         */
        public function parseUserAgent(string $userAgent, array $jsParseBits = []): UserAgent
        {
            $ua = new UserAgent();

            if (isset($jsParseBits['js_user_agent_family']) && $jsParseBits['js_user_agent_family'])
            {

                $ua->family = $jsParseBits['js_user_agent_family'];
                $ua->major = $jsParseBits['js_user_agent_v1'];
                $ua->minor = $jsParseBits['js_user_agent_v2'];
                $ua->patch = $jsParseBits['js_user_agent_v3'];

            }
            else
            {

                [$regex, $matches] = self::tryMatch($this->regexes['user_agent_parsers'], $userAgent);

                if ($matches)
                {
                    $ua->family = self::multiReplace($regex, 'family_replacement', $matches[1], $matches) ?? $ua->family;
                    $ua->major = self::multiReplace($regex, 'v1_replacement', $matches[2], $matches);
                    $ua->minor = self::multiReplace($regex, 'v2_replacement', $matches[3], $matches);
                    $ua->patch = self::multiReplace($regex, 'v3_replacement', $matches[4], $matches);
                }
            }

            if (isset($jsParseBits['js_user_agent_string']))
            {
                $jsUserAgentString = $jsParseBits['js_user_agent_string'];
                if (strpos($jsUserAgentString, 'Chrome/') !== false && strpos($userAgent, 'chromeframe') !== false)
                {
                    $override = $this->parseUserAgent($jsUserAgentString);
                    $family = $ua->family;
                    $ua->family = 'Chrome Frame';

                    if ($family !== null && $ua->major !== null)
                        $ua->family .= sprintf(' (%s %s)', $family, $ua->major);

                    $ua->major = $override->major;
                    $ua->minor = $override->minor;
                    $ua->patch = $override->patch;
                }
            }

            return $ua;
        }

    }
