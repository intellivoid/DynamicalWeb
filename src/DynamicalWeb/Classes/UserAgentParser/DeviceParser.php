<?php

    namespace DynamicalWeb\Classes\UserAgentParser;

    use DynamicalWeb\Abstracts\AbstractParser;
    use DynamicalWeb\Objects\UserAgentDevice;

    class DeviceParser extends AbstractParser
    {
        /**
         * Attempts to see if the user agent matches a device regex
         *
         * @param string $userAgent
         * @return UserAgentDevice
         */
        public function parseDevice(string $userAgent): UserAgentDevice
        {
            $device = new UserAgentDevice();

            [$regex, $matches] = self::tryMatch($this->regexes['device_parsers'], $userAgent);

            if ($matches) {
                $device->family = self::multiReplace($regex, 'device_replacement', $matches[1], $matches) ?? $device->family;
                $device->brand = self::multiReplace($regex, 'brand_replacement', null, $matches);
                $deviceModelDefault = $matches[1] !== 'Other' ? $matches[1] : null;
                $device->model = self::multiReplace($regex, 'model_replacement', $deviceModelDefault, $matches);
            }

            return $device;
        }
    }
